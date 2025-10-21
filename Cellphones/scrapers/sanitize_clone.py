"""Sanitize a scraped HTML file into a self-contained clone-like UI without navigation.

Usage:
    python sanitize_clone.py INPUT_HTML --base-url BASE_URL --out OUT_DIR [--no-assets]

What it does:
- Removes all <script> tags and inline event handler attributes (on*) to avoid client-side redirects
- Removes <base> and meta refresh tags
- Rewrites <a href> to '#' for any non-anchor or external links to prevent navigation
- Rewrites <form action> to empty and method to GET
- Optionally downloads assets (images, stylesheets, fonts) and rewrites their URLs to local files
- Writes sanitized HTML to OUT_DIR/sanitized_<originalname>.html

This is a conservative sanitizer intended for creating a static UI snapshot that won't navigate back to the original site.
"""

import argparse
import os
import re
import sys
from urllib.parse import urljoin, urlparse

import requests
from bs4 import BeautifulSoup


def safe_filename(path: str) -> str:
    path = re.sub(r"[\\\\\?\*\:\"<>|]", "_", path)
    path = path.strip()
    return path or "index.html"


def ensure_dir(path: str) -> None:
    if not path:
        return
    os.makedirs(path, exist_ok=True)


def download_asset(session: requests.Session, url: str, out_dir: str) -> str:
    """Download asset and return local relative path. If failed, return original url."""
    try:
        r = session.get(url, timeout=20)
        r.raise_for_status()
    except Exception:
        return url
    parsed = urlparse(url)
    # build a safe path under out_dir/assets/<netloc>/<path>
    netloc = parsed.netloc.replace(':', '_')
    path = parsed.path
    if path.endswith('/') or not os.path.splitext(path)[1]:
        # choose filename depending on extensionless path
        filename = safe_filename(path.lstrip('/'))
    else:
        filename = safe_filename(path.lstrip('/'))
    local_dir = os.path.join(out_dir, 'assets', netloc, os.path.dirname(filename))
    ensure_dir(local_dir)
    local_path = os.path.join(local_dir, os.path.basename(filename))
    try:
        with open(local_path, 'wb') as f:
            f.write(r.content)
        rel = os.path.relpath(local_path, out_dir)
        return rel.replace('\\\\', '/')
    except Exception:
        return url


def is_external_link(base_url: str, href: str) -> bool:
    if not href:
        return False
    parsed_href = urlparse(href)
    if not parsed_href.netloc:
        return False
    base = urlparse(base_url)
    return (parsed_href.scheme, parsed_href.netloc) != (base.scheme, base.netloc)


def sanitize_html(input_path: str, base_url: str, out_dir: str, download_assets_flag: bool = True) -> str:
    ensure_dir(out_dir)
    with open(input_path, 'r', encoding='utf-8') as f:
        html = f.read()
    soup = BeautifulSoup(html, 'html.parser')

    session = requests.Session()
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36'
    })

    # Remove base tag
    for base in soup.find_all('base'):
        base.decompose()

    # Remove meta refresh
    for meta in soup.find_all('meta'):
        if meta.get('http-equiv', '').lower() == 'refresh':
            meta.decompose()

    # Remove all scripts (external and inline) to prevent hydration and navigation
    for script in soup.find_all('script'):
        script.decompose()

    # Remove inline event handlers
    for tag in soup.find_all(True):
        attrs = dict(tag.attrs)
        for a in attrs:
            if a.lower().startswith('on'):
                try:
                    del tag.attrs[a]
                except KeyError:
                    pass

    # Process assets: link rel=stylesheet, img[src], source[src], video[src], audio[src], link[rel=preload] fonts
    asset_tags = []
    for tag in soup.find_all(['link', 'img', 'source', 'video', 'audio']):
        if tag.name == 'link' and tag.get('rel') and 'stylesheet' in tag.get('rel'):
            href = tag.get('href')
            if href:
                asset_tags.append((tag, 'href', href))
        elif tag.name == 'link' and tag.get('rel') and ('preload' in tag.get('rel') or 'prefetch' in tag.get('rel')):
            href = tag.get('href')
            if href:
                asset_tags.append((tag, 'href', href))
        else:
            src = tag.get('src')
            if src:
                asset_tags.append((tag, 'src', src))

    for tag, attr, url in asset_tags:
        abs_url = urljoin(base_url, url)
        if download_assets_flag:
            local = download_asset(session, abs_url, out_dir)
            # If download failed (returned original url), leave as-is but avoid navigation
            if local and local != abs_url:
                tag[attr] = local
            else:
                # keep remote but ensure it's absolute
                tag[attr] = abs_url
        else:
            tag[attr] = abs_url

    # Rewrite anchors: prevent navigation to other pages
    for a in soup.find_all('a'):
        href = a.get('href')
        if not href or href.startswith('#'):
            a['href'] = '#'
            continue
        # If it's a javascript pseudo-link, neutralize
        if href.strip().lower().startswith('javascript:'):
            a['href'] = '#'
            continue
        # If it's external or points to site root, neutralize
        if is_external_link(base_url, href):
            a['href'] = '#'
        else:
            # local path (starts with / or relative) -> neutralize to avoid navigation
            a['href'] = '#'

    # Rewrite forms to not submit to remote endpoints
    for form in soup.find_all('form'):
        form['action'] = ''
        form['method'] = 'get'

    # Remove service worker registrations (just in case any inline script left) - already removed scripts above

    # Write sanitized output
    name = os.path.splitext(os.path.basename(input_path))[0]
    out_file = os.path.join(out_dir, f"sanitized_{name}.html")
    with open(out_file, 'wb') as f:
        f.write(str(soup).encode('utf-8'))

    return out_file


def main():
    parser = argparse.ArgumentParser(description='Sanitize scraped HTML into a clone UI without navigation')
    parser.add_argument('input', help='Path to scraped HTML file')
    parser.add_argument('--base-url', required=True, help='Base URL used when scraping (for resolving relative asset URLs)')
    parser.add_argument('--out', default='.', help='Output directory')
    parser.add_argument('--no-assets', action='store_true', help="Don't download external assets; keep absolute URLs")
    args = parser.parse_args()

    input_path = args.input
    base_url = args.base_url
    out_dir = args.out
    download_assets_flag = not args.no_assets

    out_file = sanitize_html(input_path, base_url, out_dir, download_assets_flag)
    print(f"Sanitized file written: {out_file}")


if __name__ == '__main__':
    sys.exit(main())

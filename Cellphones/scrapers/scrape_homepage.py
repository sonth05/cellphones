"""Simple scraper to fetch a URL and save HTML (and same-origin assets) locally.

This is a lightweight re-implementation of the earlier scraper for the homepage.
"""
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


def write_binary(path: str, content: bytes) -> None:
    ensure_dir(os.path.dirname(path))
    with open(path, 'wb') as f:
        f.write(content)


def is_same_origin(base: str, url: str) -> bool:
    a = urlparse(base)
    b = urlparse(url)
    if not b.netloc:
        return True
    return (a.scheme, a.netloc) == (b.scheme, b.netloc)


def fetch(session: requests.Session, url: str):
    r = session.get(url, timeout=20)
    r.raise_for_status()
    return r


def local_path_for_url(base_url: str, url: str, html_doc: bool = False) -> str:
    u = urlparse(urljoin(base_url, url))
    path = u.path
    if not path or path.endswith('/'):
        path = path + 'index.html' if html_doc else path + 'index'
    if html_doc and not path.endswith('.html'):
        if path.endswith('index'):
            path = path + '.html'
        else:
            path = path + '.html'
    path = safe_filename(path.lstrip('/'))
    return path


def scrape_page(session: requests.Session, base_url: str, url: str, out_dir: str) -> str:
    r = fetch(session, url)
    html = r.text
    soup = BeautifulSoup(html, 'html.parser')

    assets = []
    for tag, attr in (('link','href'), ('script','src'), ('img','src')):
        for el in soup.find_all(tag):
            src = el.get(attr)
            if not src:
                continue
            abs_url = urljoin(url, src)
            if not is_same_origin(base_url, abs_url):
                continue
            assets.append((el, attr, abs_url))

    for el, attr, abs_url in assets:
        rel_path = local_path_for_url(base_url, abs_url)
        target_path = os.path.join(out_dir, rel_path)
        try:
            rr = fetch(session, abs_url)
            write_binary(target_path, rr.content)
            el[attr] = rel_path.replace('\\\\', '/')
        except Exception:
            continue

    rel_html = local_path_for_url(base_url, url, html_doc=True)
    target_html = os.path.join(out_dir, rel_html)
    write_binary(target_html, soup.prettify('utf-8'))
    return target_html


def main():
    if len(sys.argv) < 2:
        print('Usage: scrape_homepage.py URL [OUT_DIR]')
        return 1
    url = sys.argv[1]
    out = sys.argv[2] if len(sys.argv) > 2 else 'mirror/cellphones'
    ensure_dir(out)
    session = requests.Session()
    session.headers.update({'User-Agent':'Mozilla/5.0'})
    try:
        saved = scrape_page(session, url, url, out)
        print('Saved:', saved)
    except Exception as e:
        print('Error:', e)
        return 1
    return 0


if __name__ == '__main__':
    sys.exit(main())

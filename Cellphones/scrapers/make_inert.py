"""Make a sanitized HTML fully inert: convert <a> to <span>, change submit buttons to type=button.

Usage:
    python make_inert.py INPUT_HTML --out OUT_HTML
"""
import argparse
import os
import sys
from bs4 import BeautifulSoup


def make_inert(input_path: str, out_path: str) -> None:
    with open(input_path, 'r', encoding='utf-8') as f:
        soup = BeautifulSoup(f, 'html.parser')

    # Convert <a> to <span>, keep classes/id/style but remove href
    for a in soup.find_all('a'):
        span = soup.new_tag('span')
        # copy attributes except href and role
        for k, v in a.attrs.items():
            if k.lower() in ('href', 'role', 'target'):
                continue
            span.attrs[k] = v
        # add data-original-href for reference (optional)
        if a.get('href'):
            span['data-original-href'] = a['href']
        # move contents (preserve nested tags)
        for child in list(a.contents):
            span.append(child)
        a.replace_with(span)

    # Change submit buttons to type=button
    for btn in soup.find_all('button'):
        t = btn.get('type','').lower()
        if t == 'submit' or t == '':
            btn['type'] = 'button'

    # Also ensure forms won't submit: set onsubmit to return false and action to empty
    for form in soup.find_all('form'):
        form['action'] = ''
        form['method'] = 'get'
        form['onsubmit'] = 'return false;'

    # Write output
    with open(out_path, 'wb') as f:
        f.write(str(soup).encode('utf-8'))


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('input')
    parser.add_argument('--out', required=False)
    args = parser.parse_args()
    out = args.out or args.input.replace('.html','_inert.html')
    make_inert(args.input, out)
    print('Wrote:', out)

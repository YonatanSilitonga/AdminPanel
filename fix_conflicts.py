import os
import re

def resolve_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if '<<<<<<< HEAD' not in content:
        return
        
    print(f'Resolving {filepath}')
    
    pattern_generic = re.compile(r'<<<<<<< HEAD[\r]?\n(.*?)[\r]?\n=======[\r]?\n(.*?)[\r]?\n>>>>>>> [a-f0-9]+[\r]?\n', re.DOTALL)

    def replacer(match):
        head_text = match.group(1)
        theirs_text = match.group(2)
        
        if 'name="opening_hours_start"' in theirs_text:
            return theirs_text + '\n'
        elif 'csrfToken' in theirs_text:
            return theirs_text + '\n'
        elif 'hasPages()' in theirs_text and 'hasPages()' not in head_text:
            return theirs_text + '\n'
        elif 'hasPages()' in head_text and 'hasPages()' not in theirs_text:
            return head_text + '\n'
        elif 'window.dispatchEvent' in theirs_text or 'document.querySelector' in theirs_text:
            return head_text + '\n'
        else:
            return head_text + '\n'

    new_content = pattern_generic.sub(replacer, content)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)

for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.php'):
            resolve_file(os.path.join(root, file))

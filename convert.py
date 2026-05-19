import os
import re
import glob

def process_files():
    # Find all PHP files in the current directory
    php_files = glob.glob('*.php')
    
    # Open the existing style.css and main.js in append mode ('a')
    with open('style.css', 'a', encoding='utf-8') as css_file, \
         open('main.js', 'a', encoding='utf-8') as js_file:
         
        for file in php_files:
            with open(file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 1. Extract and remove <style> tags
            styles = re.findall(r'<style[^>]*>(.*?)</style>', content, re.DOTALL | re.IGNORECASE)
            for style in styles:
                if style.strip():
                    css_file.write(f"\n\n/* ==========================================\n   Extracted from {file}\n   ========================================== */\n")
                    css_file.write(style.strip() + "\n")
            # Remove the <style> tags from the HTML document
            content = re.sub(r'<style[^>]*>.*?</style>', '', content, flags=re.DOTALL | re.IGNORECASE)
            
            # 2. Extract and remove <script> tags (ignoring external scripts with 'src')
            scripts = re.findall(r'<script[^>]*>(.*?)</script>', content, re.DOTALL | re.IGNORECASE)
            for script in scripts:
                if script.strip(): 
                    js_file.write(f"\n\n/* ==========================================\n   Extracted from {file}\n   ========================================== */\n")
                    js_file.write(script.strip() + "\n")
                    
            # Remove inline <script> tags but keep external ones (like CDNs)
            content = re.sub(r'<script[^>]*>.*?</script>', 
                             lambda m: m.group(0) if 'src=' in m.group(0) else '', 
                             content, flags=re.DOTALL | re.IGNORECASE)
            
            # 3. Strip out PHP tags to make it pure static HTML
            # Removes standard <?php ... ?> tags
            content = re.sub(r'<\?php.*?\?>', '', content, flags=re.DOTALL | re.IGNORECASE)
            # Removes shorthand <?= ... ?> echo tags
            content = re.sub(r'<\?=.*?\?>', '', content, flags=re.DOTALL | re.IGNORECASE)
            
            # 4. Save the cleaned content as a new .html file
            html_filename = file.replace('.php', '.html')
            
            # Update internal links from .php to .html
            content = re.sub(r'href="([^"]+)\.php"', r'href="\1.html"', content, flags=re.IGNORECASE)
            content = re.sub(r"href='([^']+)\.php'", r"href='\1.html'", content, flags=re.IGNORECASE)

            with open(html_filename, 'w', encoding='utf-8') as f:
                f.write(content.strip())
                
            print(f"✅ Converted: {file} -> {html_filename}")

if __name__ == "__main__":
    print("Starting conversion process...")
    process_files()
    print("Conversion complete! Check style.css and main.js for your extracted code.")
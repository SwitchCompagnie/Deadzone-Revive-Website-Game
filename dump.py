import os

PROJECT_PATH = os.getcwd()
OUTPUT_FILE = "laravel_project_dump.txt"

def should_include(full_path):
    rel_path = os.path.normpath(os.path.relpath(full_path, PROJECT_PATH))
    if rel_path == ".env":
        return True
    if rel_path == "composer.json":
        return True
    if rel_path == ".env.example":
        return False
    if rel_path.startswith(os.path.normpath("routes")) and full_path.endswith(".php"):
        return True
    if rel_path.startswith(os.path.normpath("app")) and full_path.endswith(".php"):
        return True
    if rel_path.startswith(os.path.normpath("app/Models")) and full_path.endswith(".php"):
        return True
    if rel_path.startswith(os.path.normpath("resources/views")) and (full_path.endswith(".php") or full_path.endswith(".blade.php")):
        return True
    if rel_path.startswith(os.path.normpath("database")) and full_path.endswith(".php"):
        return True
    return False

with open(OUTPUT_FILE, "w", encoding="utf-8") as out_file:
    for root, dirs, files in os.walk(PROJECT_PATH):
        for file in files:
            full_path = os.path.join(root, file)
            if should_include(full_path):
                rel_path = os.path.normpath(os.path.relpath(full_path, PROJECT_PATH))
                try:
                    with open(full_path, "r", encoding="utf-8") as f:
                        content = f.read()
                except UnicodeDecodeError:
                    try:
                        with open(full_path, "r", encoding="latin-1") as f:
                            content = f.read()
                    except:
                        content = ""
                out_file.write("\n" + "="*80 + "\n")
                out_file.write(f"📄 FICHIER : {rel_path}\n")
                out_file.write("="*80 + "\n\n")
                out_file.write(content)
                out_file.write("\n\n")

print(f"✅ Extraction terminée : {OUTPUT_FILE}")
import os
import re

views_dir = r"c:\xamppp\htdocs\TNVS\resources\views"
app_dir = r"c:\xamppp\htdocs\TNVS\app"

replacements = [
    (r"VinFast All-Electric EV", "Hirna Mobility Fleet"),
    (r"VinFast EV", "Hirna Vehicle"),
    (r"VinFast EVs", "Hirna Vehicles"),
    (r"VinFast EV Fleet", "Hirna Mobility Fleet"),
    (r"VinFast EV Unit", "Hirna Fleet Vehicle"),
    (r"VinFast EV Model", "Hirna Vehicle Model"),
    (r"VinFast Nerio Green", "Toyota Vios Hirna Taxi"),
    (r"VinFast VF 8", "Hyundai Accent Hirna Taxi"),
    (r"VinFast VF e34", "Nissan Almera Hirna Taxi"),
    (r"VinFast VF 5", "Toyota Innova Fleet MPV"),
    (r"VinFast VF 9", "Toyota HiAce Shuttle Van"),
    (r"VINFAST EV UNIT", "HIRNA FLEET VEHICLE"),
    (r"VINFAST MODEL", "HIRNA MODEL"),
    (r"VINFAST", "HIRNA"),
    (r"VinFast", "Hirna"),
    (r"EV-5421", "TXI-5421"),
    (r"EV-9876", "TXI-9876"),
    (r"EV-1122", "TXI-1122"),
    (r"EV-5634", "MPV-5634"),
    (r"EV-4509", "VAN-4509"),
    (r"EV-2026-01", "TXI-5421"),
    (r"EV-2026-03", "TXI-1122"),
    (r"EV-2026-05", "VAN-4509"),
    (r"NCS-8812", "TXI-9876"),
    (r"All-Electric", "Multi-Fuel & Fleet"),
    (r"EV Sedan", "Taxi Sedan"),
    (r"EV SUV", "Fleet MPV"),
    (r"EV Crossover", "Fleet Sedan"),
    (r"EV Compact", "Fleet MPV"),
    (r"EV Premium EV", "Shuttle Van"),
    (r"Cyan EV", "Hirna Fleet"),
    (r"Cyan Compact EV", "Hirna Fleet"),
    (r"Cyan Premium EV", "Hirna Fleet"),
]

def sanitize_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    for pattern, repl in replacements:
        content = re.sub(pattern, repl, content)

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {filepath}")

for root, dirs, files in os.walk(views_dir):
    for file in files:
        if file.endswith('.php') or file.endswith('.blade.php'):
            sanitize_file(os.path.join(root, file))

for root, dirs, files in os.walk(app_dir):
    for file in files:
        if file.endswith('.php'):
            sanitize_file(os.path.join(root, file))

print("Sanitization completed!")

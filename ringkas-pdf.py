from pathlib import Path
from pypdf import PdfReader

folder = Path("storage/app/backup-naskah-masuk")

for pdf in sorted(folder.glob("*.pdf")):
    print("\n" + "=" * 70)
    print("FILE:", pdf.name)

    try:
        reader = PdfReader(str(pdf))
        print("HALAMAN:", len(reader.pages))

        for i, page in enumerate(reader.pages[:2], start=1):
            text = (page.extract_text() or "").strip()
            lines = [line.strip() for line in text.splitlines() if line.strip()]
            print(f"Halaman {i}:")
            print(" | ".join(lines[:12]) if lines else "[Tidak ada teks]")
    except Exception as e:
        print("ERROR:", repr(e))

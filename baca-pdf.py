from pathlib import Path
from pypdf import PdfReader

folder = Path("storage/app/backup-naskah-masuk")
pdf = folder / "naskah_masuk_1789973795.pdf"

try:
    reader = PdfReader(str(pdf))
    print("Nama file:", pdf.name)
    print("Jumlah halaman:", len(reader.pages))
    print("Metadata PDF:", reader.metadata)

    for i, page in enumerate(reader.pages, start=1):
        text = page.extract_text() or ""
        print(f"\n--- HALAMAN {i} ---")
        print(text[:5000] if text.strip() else "[Tidak ada teks yang bisa diekstrak]")
except Exception as e:
    print("ERROR:", repr(e))

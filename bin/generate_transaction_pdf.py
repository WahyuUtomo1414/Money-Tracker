#!/usr/bin/env python3

import json
import os
import sys

from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import mm
from reportlab.platypus import Image, Paragraph, SimpleDocTemplate, Spacer, Table, TableStyle


def money_receipt(payload_path: str, output_path: str) -> None:
    with open(payload_path, "r", encoding="utf-8") as payload_file:
        payload = json.load(payload_file)

    os.makedirs(os.path.dirname(output_path), exist_ok=True)

    doc = SimpleDocTemplate(
        output_path,
        pagesize=A4,
        rightMargin=18 * mm,
        leftMargin=18 * mm,
        topMargin=18 * mm,
        bottomMargin=18 * mm,
    )

    styles = getSampleStyleSheet()
    styles.add(
        ParagraphStyle(
            name="HeaderTitle",
            parent=styles["Heading1"],
            fontName="Helvetica-Bold",
            fontSize=20,
            leading=24,
            textColor=colors.white,
        )
    )
    styles.add(
        ParagraphStyle(
            name="HeaderSubTitle",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=9,
            leading=12,
            textColor=colors.white,
        )
    )
    styles.add(
        ParagraphStyle(
            name="SectionTitle",
            parent=styles["Heading2"],
            fontName="Helvetica-Bold",
            fontSize=12,
            leading=14,
            textColor=colors.HexColor("#163A8C"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="Label",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=9,
            leading=12,
            textColor=colors.HexColor("#64748B"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="Value",
            parent=styles["BodyText"],
            fontName="Helvetica-Bold",
            fontSize=10,
            leading=13,
            textColor=colors.HexColor("#0F172A"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="Body",
            parent=styles["BodyText"],
            fontName="Helvetica",
            fontSize=10,
            leading=14,
            alignment=TA_LEFT,
            textColor=colors.HexColor("#334155"),
        )
    )
    styles.add(
        ParagraphStyle(
            name="Amount",
            parent=styles["Heading1"],
            fontName="Helvetica-Bold",
            fontSize=24,
            leading=28,
            textColor=colors.HexColor("#15803D"),
        )
    )

    elements = []

    header = Table(
        [[
            Paragraph(payload["app_name"], styles["HeaderTitle"]),
            Paragraph("BUKTI TRANSAKSI<br/><font size='9'>Dokumen ini dibuat otomatis oleh sistem</font>", styles["HeaderSubTitle"]),
        ]],
        colWidths=[85 * mm, 85 * mm],
    )
    header.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#163A8C")),
                ("BOX", (0, 0), (-1, -1), 0, colors.HexColor("#163A8C")),
                ("LEFTPADDING", (0, 0), (-1, -1), 16),
                ("RIGHTPADDING", (0, 0), (-1, -1), 16),
                ("TOPPADDING", (0, 0), (-1, -1), 16),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 16),
                ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
                ("ALIGN", (1, 0), (1, 0), "RIGHT"),
            ]
        )
    )
    elements.append(header)
    elements.append(Spacer(1, 10))

    amount_card = Table(
        [[
            Paragraph("Nomor Transaksi", styles["Label"]),
            Paragraph("Status", styles["Label"]),
        ], [
            Paragraph(payload["transaction_no"], styles["Value"]),
            Paragraph("BERHASIL", styles["Value"]),
        ], [
            Paragraph("Nominal Transaksi", styles["Label"]),
            Paragraph("Tanggal Input", styles["Label"]),
        ], [
            Paragraph(payload["amount"], styles["Amount"]),
            Paragraph(payload["created_at"], styles["Value"]),
        ]],
        colWidths=[96 * mm, 74 * mm],
    )
    amount_card.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#F8FAFC")),
                ("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#CBD5E1")),
                ("INNERGRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#E2E8F0")),
                ("LEFTPADDING", (0, 0), (-1, -1), 14),
                ("RIGHTPADDING", (0, 0), (-1, -1), 14),
                ("TOPPADDING", (0, 0), (-1, -1), 10),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
                ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
            ]
        )
    )
    elements.append(amount_card)
    elements.append(Spacer(1, 14))

    elements.append(Paragraph("Rincian Transaksi", styles["SectionTitle"]))
    elements.append(Spacer(1, 8))

    rows = [
        [Paragraph("Tipe Transaksi", styles["Label"]), Paragraph(payload["transaction_type"], styles["Value"])],
        [Paragraph("Tanggal Transaksi", styles["Label"]), Paragraph(payload["transaction_date"], styles["Value"])],
        [Paragraph("Rekening", styles["Label"]), Paragraph(payload["wallet_name"], styles["Value"])],
        [Paragraph("Kategori", styles["Label"]), Paragraph(payload["category_name"], styles["Value"])],
        [Paragraph("Target Tabungan", styles["Label"]), Paragraph(payload["goal_name"], styles["Value"])],
        [Paragraph("Dibuat Oleh", styles["Label"]), Paragraph(payload["created_by_name"], styles["Value"])],
        [Paragraph("Email Penginput", styles["Label"]), Paragraph(payload["created_by_email"], styles["Value"])],
        [Paragraph("Deskripsi", styles["Label"]), Paragraph(payload["description"], styles["Body"])],
    ]

    details = Table(rows, colWidths=[48 * mm, 122 * mm])
    details.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                ("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#CBD5E1")),
                ("INNERGRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#E2E8F0")),
                ("LEFTPADDING", (0, 0), (-1, -1), 12),
                ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                ("TOPPADDING", (0, 0), (-1, -1), 8),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 8),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
            ]
        )
    )
    elements.append(details)

    image_path = payload.get("image_path")
    if image_path and os.path.exists(image_path):
        elements.append(Spacer(1, 14))
        elements.append(Paragraph("Lampiran Bukti Gambar", styles["SectionTitle"]))
        elements.append(Spacer(1, 8))
        proof_image = Image(image_path, width=110 * mm, height=70 * mm, kind="proportional")
        proof_table = Table([[proof_image]])
        proof_table.setStyle(
            TableStyle(
                [
                    ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                    ("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#CBD5E1")),
                    ("LEFTPADDING", (0, 0), (-1, -1), 12),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                    ("TOPPADDING", (0, 0), (-1, -1), 12),
                    ("BOTTOMPADDING", (0, 0), (-1, -1), 12),
                ]
            )
        )
        elements.append(proof_table)

    elements.append(Spacer(1, 18))
    footer = Table(
        [[Paragraph("Dokumen ini merupakan bukti transaksi yang dibuat oleh sistem Money Tracker. Simpan PDF ini sebagai arsip transaksi Anda.", styles["Body"])]],
        colWidths=[170 * mm],
    )
    footer.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, -1), colors.HexColor("#EFF6FF")),
                ("BOX", (0, 0), (-1, -1), 0.6, colors.HexColor("#BFDBFE")),
                ("LEFTPADDING", (0, 0), (-1, -1), 12),
                ("RIGHTPADDING", (0, 0), (-1, -1), 12),
                ("TOPPADDING", (0, 0), (-1, -1), 10),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 10),
            ]
        )
    )
    elements.append(footer)

    doc.build(elements)


if __name__ == "__main__":
    if len(sys.argv) != 3:
        raise SystemExit("Usage: generate_transaction_pdf.py <payload.json> <output.pdf>")

    money_receipt(sys.argv[1], sys.argv[2])

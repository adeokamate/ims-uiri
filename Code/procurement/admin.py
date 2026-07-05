from django.contrib import admin
from .models import Supplier, AcquisitionBatch, BatchItem


@admin.register(Supplier)
class SupplierAdmin(admin.ModelAdmin):
    list_display = (
        "name",
        "contact_person",
        "phone",
        "email",
    )

    search_fields = (
        "name",
        "contact_person",
    )


@admin.register(AcquisitionBatch)
class AcquisitionBatchAdmin(admin.ModelAdmin):
    list_display = (
        "reference_number",
        "supplier",
        "invoice_number",
        "received_date",
        "received_by",
    )

    list_filter = (
        "supplier",
        "received_date",
    )

    search_fields = (
        "reference_number",
        "invoice_number",
    )


@admin.register(BatchItem)
class BatchItemAdmin(admin.ModelAdmin):
    list_display = (
        "batch",
        "item",
        "quantity",
        "unit_price",
    )

    list_filter = (
        "batch",
    )

    search_fields = (
        "item__name",
    )
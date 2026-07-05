from django.contrib import admin

# Register your models here.
from django.contrib import admin
from .models import (
    TransactionType,
    StockTransaction,
    Requisition,
    RequisitionLine,
)


@admin.register(TransactionType)
class TransactionTypeAdmin(admin.ModelAdmin):
    list_display = ("name",)
    search_fields = ("name",)


@admin.register(StockTransaction)
class StockTransactionAdmin(admin.ModelAdmin):
    list_display = (
        "item",
        "transaction_type",
        "quantity",
        "performed_by",
        "transaction_date",
    )

    list_filter = (
        "transaction_type",
        "transaction_date",
    )

    search_fields = (
        "item__name",
    )


@admin.register(Requisition)
class RequisitionAdmin(admin.ModelAdmin):
    list_display = (
        "id",
        "section",
        "requested_by",
        "status",
    )

    list_filter = (
        "status",
        "section",
    )


@admin.register(RequisitionLine)
class RequisitionLineAdmin(admin.ModelAdmin):
    list_display = (
        "requisition",
        "item",
        "quantity",
    )

    search_fields = (
        "item__name",
    )
from django.contrib import admin
from .models import (
    InventoryCategory,
    UnitOfMeasure,
    ItemType,
    InventoryItem,
    AssetStatus,
    AssetInstance,
    InventoryAssignment,
)


@admin.register(InventoryCategory)
class InventoryCategoryAdmin(admin.ModelAdmin):
    list_display = ("name", "is_active")
    search_fields = ("name",)


@admin.register(UnitOfMeasure)
class UnitOfMeasureAdmin(admin.ModelAdmin):
    list_display = ("name",)
    search_fields = ("name",)


@admin.register(ItemType)
class ItemTypeAdmin(admin.ModelAdmin):
    list_display = ("name",)
    search_fields = ("name",)


@admin.register(AssetStatus)
class AssetStatusAdmin(admin.ModelAdmin):
    list_display = ("name",)
    search_fields = ("name",)


@admin.register(InventoryItem)
class InventoryItemAdmin(admin.ModelAdmin):
    list_display = (
        "name",
        "category",
        "item_type",
        "section",
        "unit",
        "reorder_level",
    )

    list_filter = (
        "category",
        "item_type",
        "section",
    )

    search_fields = (
        "name",
        "description",
    )


@admin.register(AssetInstance)
class AssetInstanceAdmin(admin.ModelAdmin):
    list_display = (
        "item",
        "serial_number",
        "asset_tag",
        "status",
        "location",
        "purchase_year",
    )

    list_filter = (
        "status",
        "location",
    )

    search_fields = (
        "serial_number",
        "asset_tag",
        "item__name",
    )


@admin.register(InventoryAssignment)
class InventoryAssignmentAdmin(admin.ModelAdmin):
    list_display = (
        "asset",
        "assigned_to",
        "assigned_date",
        "returned_date",
    )

    list_filter = (
        "assigned_date",
    )

    search_fields = (
        "asset__serial_number",
        "assigned_to__username",
    )
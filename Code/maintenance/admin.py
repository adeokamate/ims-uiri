from django.contrib import admin
from .models import MaintenanceRecord


@admin.register(MaintenanceRecord)
class MaintenanceRecordAdmin(admin.ModelAdmin):
    list_display = (
        "asset",
        "performed_by",
        "maintenance_date",
        "cost",
    )

    list_filter = (
        "maintenance_date",
    )

    search_fields = (
        "asset__serial_number",
    )
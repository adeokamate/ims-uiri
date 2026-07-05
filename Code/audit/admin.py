from django.contrib import admin
from .models import AuditLog


@admin.register(AuditLog)
class AuditLogAdmin(admin.ModelAdmin):
    list_display = (
        "user",
        "action",
        "model_name",
        "object_id",
        "ip_address",
        "timestamp",
    )
    list_filter = ("action", "model_name", "timestamp")
    search_fields = ("user__username", "action", "model_name", "object_id", "details")
    readonly_fields = (
        "user",
        "action",
        "model_name",
        "object_id",
        "details",
        "ip_address",
        "timestamp",
        "created_at",
        "updated_at",
        "is_active",
    )
    ordering = ("-timestamp",)
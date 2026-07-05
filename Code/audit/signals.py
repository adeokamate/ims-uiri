from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver

from audit.middleware import get_current_request
from audit.models import AuditLog


AUDITED_MODELS = [
    "Campus",
    "Section",
    "Location",
    "InventoryItem",
    "AssetInstance",
    "InventoryAssignment",
    "Supplier",
    "AcquisitionBatch",
    "BatchItem",
    "StockTransaction",
    "Requisition",
    "RequisitionLine",
    "MaintenanceRecord",
]


def get_client_ip(request):
    if not request:
        return None

    x_forwarded_for = request.META.get("HTTP_X_FORWARDED_FOR")
    if x_forwarded_for:
        return x_forwarded_for.split(",")[0].strip()

    return request.META.get("REMOTE_ADDR")


def get_logged_in_user(request):
    if not request:
        return None

    user = getattr(request, "user", None)

    if user and user.is_authenticated:
        return user

    return None


def create_audit_log(instance, action):
    request = get_current_request()

    user = get_logged_in_user(request)
    ip_address = get_client_ip(request)

    AuditLog.objects.create(
        user=user,
        action=action,
        model_name=instance.__class__.__name__,
        object_id=str(instance.pk),
        details=str(instance),
        ip_address=ip_address,
    )


@receiver(post_save)
def log_create_update(sender, instance, created, **kwargs):
    if sender.__name__ not in AUDITED_MODELS:
        return

    action = "Created" if created else "Updated"
    create_audit_log(instance, action)


@receiver(post_delete)
def log_delete(sender, instance, **kwargs):
    if sender.__name__ not in AUDITED_MODELS:
        return

    create_audit_log(instance, "Deleted")
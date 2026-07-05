from django.db import models
from core.models import BaseModel
from inventory.models import AssetInstance, AssetStatus
from accounts.models import UserAccount


class MaintenanceRecord(BaseModel):
    asset = models.ForeignKey(
        AssetInstance,
        on_delete=models.CASCADE,
        related_name="maintenance_records"
    )

    issue_reported = models.TextField()

    action_taken = models.TextField(blank=True, null=True)

    cost = models.DecimalField(
        max_digits=12,
        decimal_places=2,
        default=0
    )

    status_before = models.ForeignKey(
        AssetStatus,
        on_delete=models.SET_NULL,
        null=True,
        related_name="before_maintenance"
    )

    status_after = models.ForeignKey(
        AssetStatus,
        on_delete=models.SET_NULL,
        null=True,
        related_name="after_maintenance"
    )

    performed_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True
    )

    maintenance_date = models.DateTimeField(auto_now_add=True)

    next_service_date = models.DateField(null=True, blank=True)

    def __str__(self):
        return f"{self.asset} - Maintenance"
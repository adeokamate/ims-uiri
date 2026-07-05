from django.db import models

from core.models import BaseModel
from inventory.models import AssetInstance, AssetStatus
from accounts.models import UserAccount


class MaintenanceRecord(BaseModel):
    asset = models.ForeignKey(
        AssetInstance,
        on_delete=models.PROTECT,
        related_name="maintenance_records"
    )

    issue_reported = models.TextField()

    action_taken = models.TextField(
        blank=True,
        null=True
    )

    cost = models.DecimalField(
        max_digits=12,
        decimal_places=2,
        default=0
    )

    status_before = models.ForeignKey(
        AssetStatus,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="status_before_maintenance"
    )

    status_after = models.ForeignKey(
        AssetStatus,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="status_after_maintenance"
    )

    performed_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="maintenance_performed"
    )

    maintenance_date = models.DateTimeField(auto_now_add=True)

    next_service_date = models.DateField(
        null=True,
        blank=True
    )

    class Meta:
        ordering = ["-maintenance_date"]
        verbose_name = "Maintenance Record"
        verbose_name_plural = "Maintenance Records"

    def __str__(self):
        return f"{self.asset.asset_tag} - {self.maintenance_date:%Y-%m-%d}"
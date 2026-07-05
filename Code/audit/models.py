from django.db import models

from core.models import BaseModel
from accounts.models import UserAccount


class AuditLog(BaseModel):
    user = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="audit_logs"
    )

    action = models.CharField(max_length=255)

    model_name = models.CharField(max_length=100)

    object_id = models.CharField(
        max_length=100,
        blank=True,
        null=True
    )

    details = models.TextField(
        blank=True,
        null=True
    )

    ip_address = models.GenericIPAddressField(
        blank=True,
        null=True
    )

    timestamp = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ["-timestamp"]
        verbose_name = "Audit Log"
        verbose_name_plural = "Audit Logs"

    def __str__(self):
        username = self.user.username if self.user else "System"
        return f"{username} - {self.action}"
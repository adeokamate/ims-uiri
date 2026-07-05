from django.db import models

from accounts.models import UserAccount
from core.models import BaseModel
from inventory.models import InventoryItem
from organization.models import Location, Section


class TransactionType(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    class Meta:
        ordering = ["name"]

    def __str__(self):
        return self.name


class StockTransaction(BaseModel):
    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT,
        related_name="transactions"
    )
    transaction_type = models.ForeignKey(
        TransactionType,
        on_delete=models.PROTECT,
        related_name="transactions"
    )
    quantity = models.PositiveIntegerField()

    from_location = models.ForeignKey(
        Location,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="stock_out_transactions"
    )
    to_location = models.ForeignKey(
        Location,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="stock_in_transactions"
    )

    performed_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="stock_transactions"
    )

    transaction_date = models.DateTimeField(auto_now_add=True)
    remarks = models.TextField(blank=True, null=True)

    class Meta:
        ordering = ["-transaction_date"]

    def __str__(self):
        return f"{self.transaction_type.name} - {self.item.name} ({self.quantity})"


class Requisition(BaseModel):
    STATUS_CHOICES = [
        ("Pending", "Pending"),
        ("Approved", "Approved"),
        ("Rejected", "Rejected"),
        ("Issued", "Issued"),
        ("Cancelled", "Cancelled"),
    ]

    section = models.ForeignKey(
        Section,
        on_delete=models.PROTECT,
        related_name="requisitions"
    )
    requested_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="requisitions"
    )
    status = models.CharField(
        max_length=50,
        choices=STATUS_CHOICES,
        default="Pending"
    )

    class Meta:
        ordering = ["-created_at"]

    def __str__(self):
        return f"Requisition {self.id} - {self.section.name}"


class RequisitionLine(BaseModel):
    requisition = models.ForeignKey(
        Requisition,
        on_delete=models.CASCADE,
        related_name="lines"
    )
    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT,
        related_name="requisition_lines"
    )
    quantity = models.PositiveIntegerField()

    def __str__(self):
        return f"{self.item.name} ({self.quantity})"
from django.db import models
from core.models import BaseModel


class TransactionType(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    def __str__(self):
        return self.name
    
from inventory.models import InventoryItem
from organization.models import Location
from accounts.models import UserAccount


class StockTransaction(BaseModel):
    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT,
        related_name="transactions"
    )

    transaction_type = models.ForeignKey(
        TransactionType,
        on_delete=models.PROTECT
    )

    quantity = models.PositiveIntegerField()

    from_location = models.ForeignKey(
        Location,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="stock_out"
    )

    to_location = models.ForeignKey(
        Location,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="stock_in"
    )

    performed_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True
    )

    transaction_date = models.DateTimeField(auto_now_add=True)

    remarks = models.TextField(blank=True, null=True)

    def __str__(self):
        return f"{self.transaction_type} - {self.item.name}"
    

from organization.models import Section


class Requisition(BaseModel):
    section = models.ForeignKey(
        Section,
        on_delete=models.CASCADE,
        related_name="requisitions"
    )

    requested_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True
    )

    status = models.CharField(
        max_length=50,
        default="Pending"
    )

    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Requisition {self.id} - {self.section.name}"
    

from inventory.models import InventoryItem


class RequisitionLine(BaseModel):
    requisition = models.ForeignKey(
        Requisition,
        on_delete=models.CASCADE,
        related_name="lines"
    )

    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT
    )

    quantity = models.PositiveIntegerField()

    def __str__(self):
        return f"{self.item.name} ({self.quantity})"
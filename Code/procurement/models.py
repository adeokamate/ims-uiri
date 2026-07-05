from django.db import models
from core.models import BaseModel
from inventory.models import InventoryItem
from accounts.models import UserAccount



class Supplier(BaseModel):
    name = models.CharField(max_length=150, unique=True)
    contact_person = models.CharField(max_length=150, blank=True, null=True)
    phone = models.CharField(max_length=20, blank=True, null=True)
    email = models.EmailField(blank=True, null=True)
    address = models.TextField(blank=True, null=True)

    def __str__(self):
        return self.name


class AcquisitionBatch(BaseModel):
    supplier = models.ForeignKey(
        Supplier,
        on_delete=models.PROTECT,
        related_name="batches"
    )

    reference_number = models.CharField(max_length=100, unique=True)

    invoice_number = models.CharField(max_length=100, blank=True, null=True)

    received_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True
    )

    received_date = models.DateField()

    remarks = models.TextField(blank=True, null=True)

    def __str__(self):
        return f"Batch {self.reference_number}"
    
class BatchItem(BaseModel):
    batch = models.ForeignKey(
        AcquisitionBatch,
        on_delete=models.CASCADE,
        related_name="items"
    )

    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT
    )

    quantity = models.PositiveIntegerField()

    unit_price = models.DecimalField(
        max_digits=12,
        decimal_places=2
    )

    def __str__(self):
        return f"{self.item.name} ({self.quantity})"
from django.db import models
from core.models import BaseModel



class Manufacturer(BaseModel):
    name = models.CharField(max_length=150, unique=True)
    country = models.CharField(max_length=100, blank=True, null=True)
    website = models.URLField(blank=True, null=True)

    def __str__(self):
        return self.name
    

class InventoryCategory(BaseModel):
    name = models.CharField(max_length=100, unique=True)

    def __str__(self):
        return self.name
    
class UnitOfMeasure(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    def __str__(self):
        return self.name
    
class ItemType(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    def __str__(self):
        return self.name
    

from organization.models import Section
from accounts.models import UserAccount


class InventoryItem(BaseModel):
    item_code = models.CharField(
    max_length=50,
    unique=True
)
    
    name = models.CharField(max_length=150)

    category = models.ForeignKey(
        InventoryCategory,
        on_delete=models.PROTECT,
        related_name="items"
    )

    item_type = models.ForeignKey(
        ItemType,
        on_delete=models.PROTECT
    )
    brand = models.CharField(
    max_length=100,
    blank=True,
    null=True
)
    
    model = models.CharField(
    max_length=100,
    blank=True,
    null=True
)
    

    unit = models.ForeignKey(
        UnitOfMeasure,
        on_delete=models.PROTECT
    )

    section = models.ForeignKey(
        Section,
        on_delete=models.PROTECT,
        related_name="items"
    )

    manufacturer = models.ForeignKey(
    Manufacturer,
    on_delete=models.PROTECT,
    related_name="inventory_items",
    null=True,
    blank=True
)

    estimated_unit_cost = models.DecimalField(
    max_digits=15,
    decimal_places=2,
    default=0
)

    description = models.TextField(blank=True, null=True)

    reorder_level = models.PositiveIntegerField(default=0)

    def __str__(self):
        return self.name
    
class AssetStatus(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    def __str__(self):
        return self.name
    
from organization.models import Location


class AssetInstance(BaseModel):
    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.CASCADE,
        related_name="assets"
    )

    serial_number = models.CharField(max_length=100, unique=True)

    asset_tag = models.CharField(max_length=100, unique=True)

    status = models.ForeignKey(
        AssetStatus,
        on_delete=models.PROTECT
    )

    location = models.ForeignKey(
        Location,
        on_delete=models.PROTECT
    )

    purchase_year = models.PositiveIntegerField()

    def __str__(self):
        return f"{self.item.name} - {self.serial_number}"
    
from accounts.models import UserAccount


class InventoryAssignment(BaseModel):
    asset = models.ForeignKey(
        AssetInstance,
        on_delete=models.CASCADE,
        related_name="assignments"
    )

    assigned_to = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True
    )

    assigned_date = models.DateField()

    returned_date = models.DateField(null=True, blank=True)

    notes = models.TextField(blank=True, null=True)

    def __str__(self):
        return f"{self.asset} -> {self.assigned_to}"



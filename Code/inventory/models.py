from django.core.exceptions import ValidationError
from django.db import models

from accounts.models import UserAccount
from core.models import BaseModel
from organization.models import Section, Location


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


class InventoryItem(BaseModel):
    item_code = models.CharField(max_length=50, unique=True)
    name = models.CharField(max_length=150)

    category = models.ForeignKey(
        InventoryCategory,
        on_delete=models.PROTECT,
        related_name="items"
    )

    item_type = models.ForeignKey(
        ItemType,
        on_delete=models.PROTECT,
        related_name="items"
    )

    brand = models.CharField(max_length=100, blank=True, null=True)
    model = models.CharField(max_length=100, blank=True, null=True)

    unit = models.ForeignKey(
        UnitOfMeasure,
        on_delete=models.PROTECT,
        related_name="items"
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

    class Meta:
        ordering = ["name", "item_code"]

    def __str__(self):
        return f"{self.item_code} - {self.name}"


class AssetStatus(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    class Meta:
        verbose_name = "Asset Status"
        verbose_name_plural = "Asset Statuses"
        ordering = ["name"]

    def __str__(self):
        return self.name


class AssetInstance(BaseModel):
    item = models.ForeignKey(
        InventoryItem,
        on_delete=models.PROTECT,
        related_name="assets"
    )

    serial_number = models.CharField(max_length=100, unique=True)
    asset_tag = models.CharField(max_length=100, unique=True)

    status = models.ForeignKey(
        AssetStatus,
        on_delete=models.PROTECT,
        related_name="assets"
    )

    location = models.ForeignKey(
        Location,
        on_delete=models.PROTECT,
        related_name="assets"
    )

    purchase_year = models.PositiveIntegerField()

    class Meta:
        ordering = ["asset_tag"]

    def __str__(self):
        return f"{self.asset_tag} - {self.item.name}"


class InventoryAssignment(BaseModel):
    asset = models.ForeignKey(
        AssetInstance,
        on_delete=models.PROTECT,
        related_name="assignments"
    )

    assigned_to = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="assigned_assets"
    )

    assigned_by = models.ForeignKey(
        UserAccount,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="asset_assignments_made"
    )

    assigned_date = models.DateField()
    returned_date = models.DateField(null=True, blank=True)

    notes = models.TextField(blank=True, null=True)

    class Meta:
        ordering = ["-assigned_date"]

    def clean(self):
        if self.returned_date and self.returned_date < self.assigned_date:
            raise ValidationError("Returned date cannot be earlier than assigned date.")

        active_assignment_exists = InventoryAssignment.objects.filter(
            asset=self.asset,
            returned_date__isnull=True
        ).exclude(pk=self.pk).exists()

        if active_assignment_exists and self.returned_date is None:
            raise ValidationError("This asset is already actively assigned.")

    def __str__(self):
        return f"{self.asset} -> {self.assigned_to}"
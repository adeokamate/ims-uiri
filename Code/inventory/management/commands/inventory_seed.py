from inventory.models import (
    InventoryCategory,
    UnitOfMeasure,
    ItemType,
    Manufacturer,
    InventoryItem,
)

from inventory.seed_data.inventory_catalog import INVENTORY_CATALOG


def seed_inventory():

    print("Seeding inventory...")

    # -----------------------------------
    # 1. Create Manufacturers FIRST
    # -----------------------------------
    manufacturer_names = sorted(
        {item["manufacturer"] for item in INVENTORY_CATALOG}
    )

    for name in manufacturer_names:
        Manufacturer.objects.get_or_create(name=name)

    # -----------------------------------
    # 2. Ensure reference data exists
    # -----------------------------------
    categories_cache = {
        c.name: c for c in InventoryCategory.objects.all()
    }

    units_cache = {
        u.name: u for u in UnitOfMeasure.objects.all()
    }

    types_cache = {
        t.name: t for t in ItemType.objects.all()
    }

    manufacturers_cache = {
        m.name: m for m in Manufacturer.objects.all()
    }

    # -----------------------------------
    # 3. Create Inventory Items
    # -----------------------------------
    for item in INVENTORY_CATALOG:

        InventoryItem.objects.get_or_create(
            item_code=item["item_code"],
            defaults={
                "name": item["name"],
                "category": categories_cache[item["category"]],
                "manufacturer": manufacturers_cache[item["manufacturer"]],
                "brand": item["brand"],
                "model": item["model"],
                "unit": units_cache[item["unit"]],
                "item_type": types_cache[item["type"]],
                "estimated_unit_cost": item["cost"],
                "reorder_level": item["reorder_level"],
            },
        )

    print("Inventory seeding complete.")
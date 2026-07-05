from django.contrib.auth import get_user_model
from accounts.models import Role
from organization.models import Section

User = get_user_model()


def seed_accounts():
    print("Seeding accounts...")

    # Roles
    role_names = [
        "Administrator",
        "Store Manager",
        "Section Head",
        "Technician",
        "Auditor",
    ]

    roles = {}
    for name in role_names:
        role, _ = Role.objects.get_or_create(name=name)
        roles[name] = role

    # Administrator
    admin, created = User.objects.get_or_create(
        username="admin",
        defaults={
            "first_name": "System",
            "last_name": "Administrator",
            "email": "admin@uiri.go.ug",
            "is_staff": True,
            "is_superuser": True,
            "role": roles["Administrator"],
        },
    )

    if created:
        admin.set_password("admin123")
        admin.save()

    # Store Manager
    store_manager, created = User.objects.get_or_create(
        username="storemanager",
        defaults={
            "first_name": "Store",
            "last_name": "Manager",
            "email": "store@uiri.go.ug",
            "role": roles["Store Manager"],
            "section": Section.objects.filter(name="Administration").first(),
        },
    )

    if created:
        store_manager.set_password("password123")
        store_manager.save()

    # Technician
    technician, created = User.objects.get_or_create(
        username="technician",
        defaults={
            "first_name": "John",
            "last_name": "Technician",
            "email": "tech@uiri.go.ug",
            "role": roles["Technician"],
            "section": Section.objects.filter(name="Mechatronics").first(),
        },
    )

    if created:
        technician.set_password("password123")
        technician.save()

    # Auditor
    auditor, created = User.objects.get_or_create(
        username="auditor",
        defaults={
            "first_name": "Jane",
            "last_name": "Auditor",
            "email": "audit@uiri.go.ug",
            "role": roles["Auditor"],
            "section": Section.objects.filter(name="Administration").first(),
        },
    )

    if created:
        auditor.set_password("password123")
        auditor.save()

    print("Accounts seeded successfully.")
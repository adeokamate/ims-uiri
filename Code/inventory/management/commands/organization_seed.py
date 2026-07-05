from organization.models import Campus, Section, Location


def seed_organization():

    print("Seeding organization...")

    nakawa, _ = Campus.objects.get_or_create(name="Nakawa")
    namanve, _ = Campus.objects.get_or_create(name="Namanve")

    sections = {
        nakawa: [
            "Administration",
            "Mechatronics",
            "CNC",
            "Welding",
        ],
        namanve: [
            "Textile",
            "Forging",
            "Wood",
        ]
    }

    for campus, names in sections.items():

        for name in names:

            section, _ = Section.objects.get_or_create(
                campus=campus,
                name=name
            )

            Location.objects.get_or_create(
                section=section,
                name=f"{name} Workshop"
            )

    print("Organization complete.")
from django.contrib import admin
from .models import Campus, Section, Location


@admin.register(Campus)
class CampusAdmin(admin.ModelAdmin):
    list_display = ("name", "is_active", "created_at")
    search_fields = ("name",)
    ordering = ("name",)


@admin.register(Section)
class SectionAdmin(admin.ModelAdmin):
    list_display = ("name", "campus", "is_active")
    list_filter = ("campus",)
    search_fields = ("name",)
    ordering = ("campus", "name")


@admin.register(Location)
class LocationAdmin(admin.ModelAdmin):
    list_display = ("name", "section", "is_active")
    list_filter = ("section__campus", "section")
    search_fields = ("name",)
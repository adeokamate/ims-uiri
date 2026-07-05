from django.contrib import admin
from django.contrib.auth.admin import UserAdmin
from .models import Role, UserAccount


@admin.register(Role)
class RoleAdmin(admin.ModelAdmin):
    list_display = ("name", "is_active")
    search_fields = ("name",)


@admin.register(UserAccount)
class UserAccountAdmin(UserAdmin):
    list_display = (
        "username",
        "first_name",
        "last_name",
        "email",
        "role",
        "section",
        "is_staff",
        "is_active",
    )

    list_filter = (
        "role",
        "section",
        "is_staff",
        "is_active",
    )

    search_fields = (
        "username",
        "first_name",
        "last_name",
        "email",
    )
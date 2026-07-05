from django.db import models

# Create your models here.
from django.db import models
from django.contrib.auth.models import AbstractUser
from core.models import BaseModel
from organization.models import Section


class Role(BaseModel):
    name = models.CharField(max_length=50, unique=True)

    class Meta:
        verbose_name = "Role"
        verbose_name_plural = "Roles"

    def __str__(self):
        return self.name
    

class UserAccount(AbstractUser):
    role = models.ForeignKey(
        Role,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="users"
    )

    section = models.ForeignKey(
        Section,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="users"
    )

    employee_no = models.CharField(
        max_length=30,
        unique=True,
        null=True,
        blank=True
    )

    photo = models.ImageField(
    upload_to="users/",
    blank=True,
    null=True
)

    def __str__(self):
        return f"{self.employee_no} - {self.get_full_name()} ({self.username})"
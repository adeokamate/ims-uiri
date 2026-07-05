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

    phone = models.CharField(max_length=20, blank=True, null=True)

    def __str__(self):
        return self.username
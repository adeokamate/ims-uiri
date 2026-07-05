from django.db import models


class BaseModel(models.Model):
    """
    Abstract base model to be inherited by all models.
    Adds common audit fields.
    """

    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
    is_active = models.BooleanField(default=True)

    class Meta:
        abstract = True
from django.db import models
from core.models import BaseModel


class Campus(BaseModel):
    name = models.CharField(max_length=100, unique=True)

    class Meta:
        verbose_name = "Campus"
        verbose_name_plural = "Campuses"

    def __str__(self):
        return self.name
    

class Section(BaseModel):
    campus = models.ForeignKey(
        Campus,
        on_delete=models.CASCADE,
        related_name="sections"
    )
    name = models.CharField(max_length=100)

    class Meta:
        verbose_name = "Section"
        verbose_name_plural = "Sections"
        unique_together = ('campus', 'name')

    def __str__(self):
        return f"{self.name} ({self.campus.name})"
    
class Location(BaseModel):
    section = models.ForeignKey(
        Section,
        on_delete=models.CASCADE,
        related_name="locations"
    )
    name = models.CharField(max_length=100)

    class Meta:
        verbose_name = "Location"
        verbose_name_plural = "Locations"
        unique_together = ('section', 'name')

    def __str__(self):
        return f"{self.name} - {self.section.name}"
    
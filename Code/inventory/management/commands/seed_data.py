from django.core.management.base import BaseCommand

from .organization_seed import seed_organization
from .accounts_seed import seed_accounts
'''
from .inventory_seed import seed_inventory
from .procurement_seed import seed_procurement
from .transactions_seed import seed_transactions
from .maintenance_seed import seed_maintenance
'''
class Command(BaseCommand):
    help = "Seeds the UIRI Inventory Management System database"

    def handle(self, *args, **kwargs):

        self.stdout.write(self.style.SUCCESS("Starting database seeding...\n"))

        seed_organization()
        seed_accounts()
        '''
        seed_inventory()
        seed_procurement()
        seed_transactions()
        seed_maintenance()
        '''
        self.stdout.write(self.style.SUCCESS("\nDatabase seeded successfully!"))
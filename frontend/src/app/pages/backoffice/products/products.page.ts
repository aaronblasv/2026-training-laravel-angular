import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { ProductService } from '../../../services/api/product.service';
import { FamilyService } from '../../../services/api/family.service';
import { TaxService } from '../../../services/api/tax.service';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-products',
  templateUrl: './products.page.html',
  styleUrls: ['./products.page.scss'],
  standalone: true,
  imports: [IonContent, CommonModule, FormsModule, SidebarComponent]
})
export class ProductsPage implements OnInit {

  private productService = inject(ProductService);
  private familyService = inject(FamilyService);
  private taxService = inject(TaxService);

  products: any[] = [];
  families: any[] = [];
  taxes: any[] = [];
  showForm = false;
  editingProduct: any = null;

  form = {
    name: '',
    price: 0,
    stock: 0,
    familyId: '',
    taxId: '',
  };

  ngOnInit() {
    this.loadData();
  }

  loadData() {
    forkJoin({
      products: this.productService.getAll(),
      families: this.familyService.getAll(),
      taxes: this.taxService.getAll(),
    }).subscribe({
      next: ({ products, families, taxes }) => {
        console.log('products:', products);
        console.log('families:', families);
        console.log('taxes:', taxes);
        this.products = products;
        this.families = families;
        this.taxes = taxes;
      },
      error: (err: any) => console.error('forkJoin error:', err)
    });
  }

  get productsByFamily(): { family: any, products: any[] }[] {
    return this.families.map(family => ({
      family,
      products: this.products.filter(p => p.familyId === family.uuid)
    })).filter(group => group.products.length > 0);
  }

  openForm(product?: any) {
    this.editingProduct = product ?? null;
    this.form = {
      name: product?.name ?? '',
      price: product?.price ?? 0,
      stock: product?.stock ?? 0,
      familyId: product?.familyId ?? '',
      taxId: product?.taxId ?? '',
    };
        this.showForm = true;
  }

  closeForm() {
    this.showForm = false;
    this.editingProduct = null;
  }

  save() {
    const action = this.editingProduct
      ? this.productService.update(this.editingProduct.uuid, this.form)
      : this.productService.create(this.form);

    action.subscribe({
      next: () => { this.loadData(); this.closeForm(); },
      error: (err: any) => console.error(err)
    });
  }

  toggle(uuid: string) {
    this.productService.toggle(uuid).subscribe({
      next: () => this.loadData(),
      error: (err: any) => console.error(err)
    });
  }

  delete(uuid: string) {
    if (confirm('¿Eliminar este producto?')) {
      this.productService.delete(uuid).subscribe({
        next: () => this.loadData(),
        error: (err: any) => console.error(err)
      });
    }
  }
}
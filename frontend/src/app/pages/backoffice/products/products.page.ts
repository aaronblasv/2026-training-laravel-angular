import { Component, OnInit, inject, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { ProductService } from '../../../services/api/product.service';
import { FamilyService } from '../../../services/api/family.service';
import { TaxService } from '../../../services/api/tax.service';
import { UploadService } from '../../../services/api/upload.service';
import { forkJoin } from 'rxjs';
import { FormModalComponent } from '../../../components/form-modal/form-modal.component';
import { ConfirmModalComponent } from '../../../components/confirm-modal/confirm-modal.component';
import { ActionButtonsComponent } from '../../../components/action-buttons/action-buttons.component';

@Component({
  selector: 'app-products',
  templateUrl: './products.page.html',
  styleUrls: ['./products.page.scss'],
  standalone: true,
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [IonContent, CommonModule, FormsModule, SidebarComponent, FormModalComponent, ConfirmModalComponent, ActionButtonsComponent]
})
export class ProductsPage implements OnInit {

  private productService = inject(ProductService);
  private familyService = inject(FamilyService);
  private taxService = inject(TaxService);
  private uploadService = inject(UploadService);

  products: any[] = [];
  families: any[] = [];
  taxes: any[] = [];
  showForm = false;
  editingProduct: any = null;
  errors: { [key: string]: string } = {};
  pendingDeleteUuid: string | null = null;
  showConfirm = false;

  form = {
    name: '',
    price: 0,
    stock: 0,
    family_id: '',
    tax_id: '',
    image_src: '' as string | null,
  };

  ngOnInit() {
    this.loadData();
  }

  requestDelete(uuid: string) {
    this.pendingDeleteUuid = uuid;
    this.showConfirm = true;
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
      products: this.products.filter(p => p.family_id === family.uuid)
    })).filter(group => group.products.length > 0);
  }

  openForm(product?: any) {
    this.editingProduct = product ?? null;
    this.form = {
      name: product?.name ?? '',
      price: product?.price ?? 0,
      stock: product?.stock ?? 0,
      family_id: product?.family_id ?? '',
      tax_id: product?.tax_id ?? '',
      image_src: product?.image_src ?? '',
    };
        this.showForm = true;
  }

  closeForm() {
    this.showForm = false;
    this.editingProduct = null;
    this.errors = {};
  }

  save() {
    this.errors = {};
    const action = this.editingProduct
      ? this.productService.update(this.editingProduct.uuid, this.form)
      : this.productService.create(this.form);

    action.subscribe({
      next: () => { this.loadData(); this.closeForm(); },
      error: (err: any) => {
        if (err.status === 422) {
          Object.keys(err.error.errors).forEach(key => {
            this.errors[key] = err.error.errors[key][0];
          });
        }
      }
    });
  }


  onFileSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.uploadService.uploadImage(file).subscribe({
      next: (url) => this.form.image_src = url,
      error: (err: any) => console.error('Upload error:', err),
    });
  }

  toggle(uuid: string) {
    const product = this.products.find(p => p.uuid === uuid);
    const action = product.active
      ? this.productService.deactivate(uuid)
      : this.productService.activate(uuid);

    action.subscribe({
      next: () => this.loadData(),
      error: (err: any) => console.error(err)
    });
  }

  confirmDelete() {
    if (!this.pendingDeleteUuid) return;
    this.productService.delete(this.pendingDeleteUuid).subscribe({
      next: () => { this.loadData(); this.closeConfirm(); },
      error: (err: any) => console.error(err)
    });
  }

  closeConfirm() {
    this.showConfirm = false;
    this.pendingDeleteUuid = null;
  }
}
import { Component, OnInit, inject } from '@angular/core';
import { CommonModule, registerLocaleData } from '@angular/common';
import { Router, ActivatedRoute } from '@angular/router';
import { IonicModule } from '@ionic/angular';
import { forkJoin } from 'rxjs';
import { OrderService } from '../../../services/api/order.service';
import { ProductService } from '../../../services/api/product.service';
import { FamilyService } from '../../../services/api/family.service';
import { TaxService } from '../../../services/api/tax.service';
import { TableService } from '../../../services/api/table.service';
import { PaymentService } from '../../../services/api/payment.service';
import { PinModalComponent } from '../../../components/pin-modal/pin-modal.component';
import { PaymentModalComponent } from '../../../components/payment-modal/payment-modal.component';
import { SuccessModalComponent } from '../../../components/success-modal/success-modal.component';
import { WaiterModalComponent } from '../../../components/waiter-modal/waiter-modal.component';
import localeEs from '@angular/common/locales/es';

registerLocaleData(localeEs);

@Component({
  selector: 'app-order',
  standalone: true,
  imports: [CommonModule, IonicModule, PinModalComponent, PaymentModalComponent, SuccessModalComponent, WaiterModalComponent],
  templateUrl: './order.page.html',
  styleUrls: ['./order.page.scss'],
})
export class OrderPage implements OnInit {
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private orderService = inject(OrderService);
  private productService = inject(ProductService);
  private familyService = inject(FamilyService);
  private taxService = inject(TaxService);
  private tableService = inject(TableService);
  private paymentService = inject(PaymentService);

  tableUuid = '';
  tableName = '';
  order: any = null;
  products: any[] = [];
  families: any[] = [];
  taxes: any[] = [];
  tables: any[] = [];
  currentUser: any = null;

  selectedFamilyUuid: string | null = null;
  showPaymentModal = false;
  showSuccessModal = false;
  showCloseWaiterModal = false;
  showClosePinModal = false;
  closeSelectedWaiter: any = null;
  totalPaid = 0;
  lastInvoiceNumber = '';
  lastTotalAmount = 0;

  ngOnInit() {
    this.tableUuid = this.route.snapshot.paramMap.get('tableUuid') || '';

    const nav = this.router.getCurrentNavigation();
    this.currentUser = nav?.extras?.state?.['user'] || history.state?.['user'];

    if (!this.currentUser) {
      this.router.navigate(['/tpv']);
      return;
    }

    // Reset all state when entering a new table
    this.resetState();
    this.loadData();
  }

  resetState() {
    this.selectedFamilyUuid = null;
    this.showPaymentModal = false;
    this.showSuccessModal = false;
    this.showCloseWaiterModal = false;
    this.showClosePinModal = false;
    this.closeSelectedWaiter = null;
    this.totalPaid = 0;
    this.lastInvoiceNumber = '';
    this.lastTotalAmount = 0;
    this.order = null;
  }

  loadData() {
    forkJoin({
      products: this.productService.getAllTpv(),
      families: this.familyService.getAllTpv(),
      taxes: this.taxService.getAllTpv(),
      tables: this.tableService.getAllTpv(),
    }).subscribe({
      next: ({ products, families, taxes, tables }) => {
        this.products = products.filter((p: any) => p.active);
        this.families = families;
        this.taxes = taxes;
        this.tables = tables;
        this.tableName = tables.find((t: any) => t.uuid === this.tableUuid)?.name || '';
        if (this.families.length > 0) {
          this.selectedFamilyUuid = this.families[0].uuid;
        }
        this.loadOrder();
      },
      error: (err) => console.error(err),
    });
  }

  loadOrder() {
    this.orderService.getOrderByTable(this.tableUuid).subscribe({
      next: (order) => {
        if (!order) {
          this.router.navigate(['/tpv']);
          return;
        }
        this.order = order;
      },
      error: () => {
        this.router.navigate(['/tpv']);
      },
    });
  }

  get filteredProducts() {
    if (!this.selectedFamilyUuid) return this.products;
    return this.products.filter(p => p.familyId === this.selectedFamilyUuid);
  }

  selectFamily(uuid: string | null) {
    this.selectedFamilyUuid = uuid;
  }

  getTaxPercentage(taxId: string): number {
    return this.taxes.find(t => t.uuid === taxId)?.percentage ?? 0;
  }

  getProductName(productId: string): string {
    return this.products.find(p => p.uuid === productId)?.name ?? 'Producto';
  }

  getLineSubtotal(line: any): number {
    return line.price * line.quantity;
  }

  getLineTax(line: any): number {
    return this.getLineSubtotal(line) * line.taxPercentage / 100;
  }

  getLineTotal(line: any): number {
    return this.getLineSubtotal(line) + this.getLineTax(line);
  }

  get orderSubtotal(): number {
    if (!this.order?.lines) return 0;
    return this.order.lines.reduce((sum: number, l: any) => sum + this.getLineSubtotal(l), 0);
  }

  get orderTax(): number {
    if (!this.order?.lines) return 0;
    return this.order.lines.reduce((sum: number, l: any) => sum + this.getLineTax(l), 0);
  }

  get orderTotal(): number {
    return this.orderSubtotal + this.orderTax;
  }

  addProduct(product: any) {
    const existingLine = this.order?.lines?.find((l: any) => l.productId === product.uuid);

    if (existingLine) {
      const newQty = existingLine.quantity + 1;
      this.orderService.updateLineQuantity(this.order.uuid, existingLine.uuid, newQty).subscribe({
        next: () => {
          existingLine.quantity = newQty;
        },
        error: (err) => console.error('Error updating line:', err),
      });
    } else {
      const taxPercentage = this.getTaxPercentage(product.taxId);
      this.orderService.addLine(this.order.uuid, product.uuid, this.currentUser.id, 1, product.price, taxPercentage).subscribe({
        next: (line) => {
          this.order.lines.push(line);
        },
        error: (err) => console.error('Error adding line:', err),
      });
    }
  }

  incrementLine(line: any) {
    const newQty = line.quantity + 1;
    this.orderService.updateLineQuantity(this.order.uuid, line.uuid, newQty).subscribe({
      next: () => {
        line.quantity = newQty;
      },
      error: (err) => console.error('Error updating line:', err),
    });
  }

  decrementLine(line: any) {
    if (line.quantity <= 1) {
      this.removeLine(line);
      return;
    }
    const newQty = line.quantity - 1;
    this.orderService.updateLineQuantity(this.order.uuid, line.uuid, newQty).subscribe({
      next: () => {
        line.quantity = newQty;
      },
      error: (err) => console.error('Error updating line:', err),
    });
  }

  removeLine(line: any) {
    this.orderService.removeLine(this.order.uuid, line.uuid).subscribe({
      next: () => {
        this.order.lines = this.order.lines.filter((l: any) => l.uuid !== line.uuid);
      },
      error: (err) => console.error('Error removing line:', err),
    });
  }

  cancelOrder() {
    this.orderService.cancelOrder(this.order.uuid).subscribe({
      next: () => {
        this.router.navigate(['/tpv'], { 
          replaceUrl: true
        });
      },
      error: (err) => console.error('Error cancelling order:', err),
    });
  }

  // Cerrar y cobrar: paso 1 → waiter modal
  requestClose() {
    this.showCloseWaiterModal = true;
  }

  onCloseWaiterSelected(waiter: any) {
    this.closeSelectedWaiter = waiter;
    this.showCloseWaiterModal = false;
    this.showClosePinModal = true;
  }

  onCloseWaiterCancelled() {
    this.showCloseWaiterModal = false;
    this.closeSelectedWaiter = null;
  }

  // Cerrar y cobrar: paso 2 → PIN validado → payment modal
  onClosePinValidated(user: any) {
    console.log('onClosePinValidated called with user:', user);
    this.showClosePinModal = false;
    this.closeSelectedWaiter = null;
    this.currentUser = user;
    console.log('currentUser set to:', this.currentUser);
    this.showPaymentModal = true;
  }

  onClosePinCancelled() {
    this.showClosePinModal = false;
    this.closeSelectedWaiter = null;
  }

  // Cerrar y cobrar: paso 3 → pagos
  onPaymentRegistered(payment: any) {
    console.log('onPaymentRegistered called with payment:', payment, 'type:', typeof payment.amount);
    // Register payment in the backend
    this.paymentService.registerPayment(
      this.order.uuid,
      parseInt(payment.amount), // Ensure it's an integer
      payment.method,
      payment.description
    ).subscribe({
      next: () => {
        console.log('Payment registered successfully');
        const paymentAmount = parseInt(payment.amount);
        console.log('Adding payment amount:', paymentAmount, 'totalPaid before:', this.totalPaid);
        this.totalPaid += paymentAmount;
        console.log('totalPaid after:', this.totalPaid, 'orderTotal:', this.orderTotal);
        console.log('Comparison: totalPaid >= orderTotal?', this.totalPaid, '>=', this.orderTotal, '=', this.totalPaid >= this.orderTotal);
        // Check if order is fully paid after this payment
        if (this.totalPaid >= this.orderTotal) {
          console.log('✓ Order is fully paid, calling onPaymentComplete');
          this.onPaymentComplete();
        } else {
          console.log('✗ Order NOT fully paid yet. Still pending:', this.orderTotal - this.totalPaid);
        }
      },
      error: (err) => {
        console.error('Error registering payment:', err);
      },
    });
  }

  onPaymentComplete() {
    console.log('onPaymentComplete called', { totalPaid: this.totalPaid, orderTotal: this.orderTotal });
    console.log('Closing payment modal and proceeding to close order');
    
    this.showPaymentModal = false;
    this.lastTotalAmount = this.orderTotal;

    if (!this.order || !this.order.uuid) {
      console.error('Order not found or invalid');
      this.showSuccessModal = true;
      return;
    }

    console.log('Calling generateInvoice for order:', this.order.uuid);
    this.paymentService.generateInvoice(this.order.uuid).subscribe({
      next: (response: any) => {
        console.log('Invoice generated successfully:', response);
        this.lastInvoiceNumber = response.invoice_number || 'INV-XXXXXX-XXXX';

        if (!this.currentUser || !this.currentUser.uuid) {
          console.error('Current user not found or invalid');
          this.showSuccessModal = true;
          return;
        }

        console.log('Calling closeOrder for order:', this.order.uuid, 'by user:', this.currentUser.uuid);
        this.orderService.closeOrder(this.order.uuid, this.currentUser.uuid).subscribe({
          next: (response) => {
            console.log('Order closed successfully:', response);
            console.log('✓ Order status changed to closed, mesa is now LIBRE');
            this.showSuccessModal = true;
          },
          error: (err) => {
            console.error('Error closing order:', err);
            // Still show success even if close fails
            this.showSuccessModal = true;
          },
        });
      },
      error: (err) => {
        console.error('Error generating invoice:', err);
        // Still try to close even if invoice fails
        if (!this.currentUser || !this.currentUser.uuid) {
          console.error('Current user not found or invalid');
          this.showSuccessModal = true;
          return;
        }

        this.orderService.closeOrder(this.order.uuid, this.currentUser.uuid).subscribe({
          next: (response) => {
            console.log('Order closed successfully (after invoice error):', response);
            this.showSuccessModal = true;
          },
          error: (closeErr) => {
            console.error('Error closing order:', closeErr);
            this.showSuccessModal = true;
          },
        });
      },
    });
  }

  onSuccessModalClose() {
    this.showSuccessModal = false;
    // Usar queryParamsHandling para forzar la recarga de la página
    this.router.navigate(['/tpv'], { 
      replaceUrl: true
    });
  }

  onPaymentCancelled() {
    this.showPaymentModal = false;
  }

  goBack() {
    this.router.navigate(['/tpv'], { 
      replaceUrl: true
    });
  }
}

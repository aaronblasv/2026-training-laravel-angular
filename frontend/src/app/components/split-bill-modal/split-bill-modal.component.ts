import { Component, Input, Output, EventEmitter, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PaymentData, PaymentMethod } from '../../types/payment.model';

interface DinerShare {
  index: number;
  amount: number;
  method: PaymentMethod;
  paid: boolean;
}

@Component({
  selector: 'app-split-bill-modal',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './split-bill-modal.component.html',
  styleUrls: ['./split-bill-modal.component.scss'],
})
export class SplitBillModalComponent implements OnChanges {
  @Input() visible = false;
  @Input() totalAmount = 0;
  @Input() diners = 1;
  @Output() onPayment = new EventEmitter<PaymentData>();
  @Output() onAllPaid = new EventEmitter<void>();
  @Output() onCancel = new EventEmitter<void>();

  shares: DinerShare[] = [];
  totalPaid = 0;

  ngOnChanges(changes: SimpleChanges) {
    if (changes['visible'] && this.visible) {
      this.totalPaid = 0;
      this.buildShares();
    }
  }

  private buildShares() {
    const count = Math.max(1, this.diners);
    const base = Math.floor(this.totalAmount / count);
    const remainder = this.totalAmount % count;
    this.shares = Array.from({ length: count }, (_, i) => ({
      index: i,
      amount: base + (i < remainder ? 1 : 0),
      method: 'cash' as PaymentMethod,
      paid: false,
    }));
  }

  get pendingAmount(): number {
    return this.totalAmount - this.totalPaid;
  }

  get allPaid(): boolean {
    return this.shares.length > 0 && this.shares.every(s => s.paid);
  }

  payShare(share: DinerShare) {
    if (share.paid || share.amount <= 0) return;
    share.paid = true;
    this.totalPaid += share.amount;

    this.onPayment.emit({
      amount: share.amount,
      method: share.method,
      description: `Comensal ${share.index + 1}/${this.shares.length}`,
    });

    if (this.allPaid) {
      this.onAllPaid.emit();
    }
  }

  formatCents(amount: number): string {
    return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(amount / 100);
  }

  cancel() {
    this.onCancel.emit();
  }
}

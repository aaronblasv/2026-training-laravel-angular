import { Component, Input, Output, EventEmitter, inject, OnInit, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';

export type PaymentMethod = 'cash' | 'card' | 'bizum';

export interface PaymentData {
  amount: number;
  method: PaymentMethod;
  description?: string;
}

@Component({
  selector: 'app-payment-modal',
  standalone: true,
  imports: [CommonModule, FormsModule, IonicModule],
  templateUrl: './payment-modal.component.html',
  styleUrls: ['./payment-modal.component.scss'],
})
export class PaymentModalComponent implements OnInit, OnChanges {
  @Input() visible = false;
  @Input() totalAmount = 0;
  @Input() paidAmount = 0;
  @Output() onPayment = new EventEmitter<PaymentData>();
  @Output() onCancel = new EventEmitter<void>();
  @Output() onComplete = new EventEmitter<void>();

  Math = Math;

  paymentMethod: PaymentMethod = 'cash';
  paymentAmount: number = 0;
  percentageMode = false;
  percentage: number = 0;
  description = '';
  tipAmount = 0;

  pendingAmount = 0;
  originalTotal = 0;
  paymentHistory: PaymentData[] = [];
  loading = false;

  ngOnInit() {
    this.updatePendingAmount();
  }

  ngOnChanges(changes: SimpleChanges) {
    if (changes['paidAmount'] || changes['totalAmount']) {
      this.updatePendingAmount();
    }
  }

  updatePendingAmount() {
    this.pendingAmount = this.totalAmount - this.paidAmount;
    this.originalTotal = this.totalAmount;
    this.paymentAmount = this.pendingAmount;
  }

  togglePercentageMode() {
    this.percentageMode = !this.percentageMode;
    if (this.percentageMode) {
      this.percentage = 0;
      this.paymentAmount = 0;
    } else {
      this.paymentAmount = this.pendingAmount;
    }
  }

  onPercentageChange() {
    if (this.percentageMode && this.percentage > 0) {
      this.paymentAmount = Math.round((this.originalTotal * this.percentage) / 100);
    }
  }

  addTip(percentage: number) {
    const tipValue = Math.round((this.originalTotal * percentage) / 100);
    // Toggle: if clicking the same percentage, deselect it
    if (this.tipAmount === tipValue) {
      this.tipAmount = 0;
      this.paymentAmount = this.pendingAmount;
    } else {
      this.tipAmount = tipValue;
      this.paymentAmount = this.pendingAmount + this.tipAmount;
    }
  }

  payFull() {
    console.log('payFull called. pendingAmount:', this.pendingAmount, 'paidAmount:', this.paidAmount, 'totalAmount:', this.totalAmount);
    this.paymentAmount = Number(this.pendingAmount);
    this.percentageMode = false;
    this.tipAmount = 0;
    this.processPayment();
  }

  processPayment() {
    const amount = Number(this.paymentAmount);
    console.log('processPayment called. paymentAmount:', amount, 'pendingAmount:', this.pendingAmount);
    if (amount <= 0) {
      console.log('Payment amount is 0 or less, returning');
      return;
    }

    // Allow paying more than pending (for tips)
    if (amount > this.pendingAmount && this.tipAmount === 0) {
      alert('El monto no puede exceder lo pendiente por pagar');
      return;
    }

    this.loading = true;

    const payment: PaymentData = {
      amount: amount,
      method: this.paymentMethod,
      description: this.description || (this.tipAmount > 0 ? `Propina de ${this.tipAmount / 100}€` : ''),
    };

    console.log('Emitting payment:', payment);
    this.onPayment.emit(payment);

    // Reset form
    this.paymentAmount = 0;
    this.percentage = 0;
    this.percentageMode = false;
    this.description = '';
    this.tipAmount = 0;
    this.loading = false;
  }

  cancel() {
    this.paymentAmount = 0;
    this.percentage = 0;
    this.percentageMode = false;
    this.description = '';
    this.tipAmount = 0;
    this.onCancel.emit();
  }
}

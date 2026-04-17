import { Component, Input, Output, EventEmitter, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

export interface DiscountResult {
  type: 'amount' | 'percentage' | null;
  value: number;
}

@Component({
  selector: 'app-discount-modal',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './discount-modal.component.html',
  styleUrls: ['./discount-modal.component.scss'],
})
export class DiscountModalComponent implements OnChanges {
  @Input() visible = false;
  @Input() title = 'Descuento';
  @Input() currentType: 'amount' | 'percentage' | null = null;
  @Input() currentValue = 0;
  @Output() onConfirm = new EventEmitter<DiscountResult>();
  @Output() onCancel = new EventEmitter<void>();

  mode: 'percentage' | 'amount' = 'percentage';
  inputValue = '';

  ngOnChanges(changes: SimpleChanges) {
    if (changes['visible'] && this.visible) {
      if (this.currentType === 'amount') {
        this.mode = 'amount';
        this.inputValue = (this.currentValue / 100).toFixed(2);
      } else if (this.currentType === 'percentage') {
        this.mode = 'percentage';
        this.inputValue = String(this.currentValue);
      } else {
        this.mode = 'percentage';
        this.inputValue = '';
      }
    }
  }

  setMode(mode: 'percentage' | 'amount') {
    this.mode = mode;
    this.inputValue = '';
  }

  removeDiscount() {
    this.onConfirm.emit({ type: null, value: 0 });
  }

  confirm() {
    const raw = this.inputValue.replace(',', '.');
    const num = Number(raw);
    if (isNaN(num) || num < 0) return;

    if (this.mode === 'percentage') {
      this.onConfirm.emit({ type: 'percentage', value: Math.round(num) });
    } else {
      this.onConfirm.emit({ type: 'amount', value: Math.round(num * 100) });
    }
  }

  cancel() {
    this.onCancel.emit();
  }
}

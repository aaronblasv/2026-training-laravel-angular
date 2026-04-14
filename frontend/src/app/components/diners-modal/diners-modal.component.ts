import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-diners-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './diners-modal.component.html',
  styleUrls: ['./diners-modal.component.scss'],
})
export class DinersModalComponent {
  @Input() visible = false;
  @Output() onConfirm = new EventEmitter<number>();
  @Output() onCancel = new EventEmitter<void>();

  value = '';

  get displayValue(): string {
    return this.value || '0';
  }

  onDigit(digit: string) {
    if (this.value.length >= 3) return;
    this.value += digit;
  }

  onDelete() {
    this.value = this.value.slice(0, -1);
  }

  onClear() {
    this.value = '';
  }

  confirm() {
    const diners = parseInt(this.value, 10);
    if (!diners || diners < 1) return;
    this.value = '';
    this.onConfirm.emit(diners);
  }

  cancel() {
    this.value = '';
    this.onCancel.emit();
  }
}

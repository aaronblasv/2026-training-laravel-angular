import { Component, Input, Output, EventEmitter, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrderService } from '../../services/api/order.service';

@Component({
  selector: 'app-pin-modal',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './pin-modal.component.html',
  styleUrls: ['./pin-modal.component.scss'],
})
export class PinModalComponent {
  private orderService = inject(OrderService);

  @Input() visible = false;
  @Input() selectedWaiter: any = null;
  @Output() onValidated = new EventEmitter<{ id: string; name: string; role: string }>();
  @Output() onCancel = new EventEmitter<void>();

  pin = '';
  error = '';
  loading = false;

  get dots(): boolean[] {
    return [0, 1, 2, 3].map(i => i < this.pin.length);
  }

  onDigit(digit: string) {
    if (this.pin.length >= 4) return;
    this.error = '';
    this.pin += digit;
    if (this.pin.length === 4) {
      this.validate();
    }
  }

  onDelete() {
    this.pin = this.pin.slice(0, -1);
    this.error = '';
  }

  onClear() {
    this.pin = '';
    this.error = '';
  }

  validate() {
    if (this.pin.length !== 4) return;
    this.loading = true;
    this.orderService.validatePin(this.pin).subscribe({
      next: (user: any) => {
        this.loading = false;
        if (this.selectedWaiter && user.id !== this.selectedWaiter.id) {
          this.error = 'El PIN no corresponde a ' + this.selectedWaiter.name;
          this.pin = '';
          return;
        }
        this.pin = '';
        this.onValidated.emit(user);
      },
      error: () => {
        this.loading = false;
        this.error = 'PIN incorrecto';
        this.pin = '';
      },
    });
  }

  cancel() {
    this.pin = '';
    this.error = '';
    this.onCancel.emit();
  }
}

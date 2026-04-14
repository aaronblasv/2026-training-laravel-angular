import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

@Component({
  selector: 'app-success-modal',
  standalone: true,
  imports: [CommonModule, IonicModule],
  templateUrl: './success-modal.component.html',
  styleUrls: ['./success-modal.component.scss'],
})
export class SuccessModalComponent {
  @Input() visible = false;
  @Input() invoiceNumber = '';
  @Input() totalAmount = 0;

  @Output() onClose = new EventEmitter<void>();

  close() {
    this.onClose.emit();
  }
}

import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-confirm-modal',
  templateUrl: './confirm-modal.component.html',
  styleUrls: ['./confirm-modal.component.scss'],
  standalone: true,
  imports: [CommonModule]
})
export class ConfirmModalComponent {
  @Input() visible: boolean = false;
  @Input() title: string = '¿Estás seguro?';
  @Input() message: string = 'Esta acción no se puede deshacer.';

  @Output() onConfirm = new EventEmitter<void>();
  @Output() onCancel = new EventEmitter<void>();

  confirm() {
    this.onConfirm.emit();
  }

  cancel() {
    this.onCancel.emit();
  }
}
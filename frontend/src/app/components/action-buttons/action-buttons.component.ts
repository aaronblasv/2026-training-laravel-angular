import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-action-buttons',
  templateUrl: './action-buttons.component.html',
  styleUrls: ['./action-buttons.component.scss'],
  standalone: true,
  imports: [CommonModule]
})
export class ActionButtonsComponent {
  @Input() showToggle: boolean = false;
  @Input() isActive: boolean = true;

  @Output() onEdit = new EventEmitter<void>();
  @Output() onDelete = new EventEmitter<void>();
  @Output() onToggle = new EventEmitter<void>();
}
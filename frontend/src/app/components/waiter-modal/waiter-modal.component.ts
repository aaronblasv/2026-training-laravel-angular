import { Component, Input, Output, EventEmitter, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { UserService } from '../../services/api/user.service';

@Component({
  selector: 'app-waiter-modal',
  standalone: true,
  imports: [CommonModule, IonicModule],
  templateUrl: './waiter-modal.component.html',
  styleUrls: ['./waiter-modal.component.scss'],
})
export class WaiterModalComponent implements OnInit {
  @Input() visible = false;
  @Output() onSelected = new EventEmitter<any>();
  @Output() onCancel = new EventEmitter<void>();

  private userService = inject(UserService);

  waiters: any[] = [];

  ngOnInit() {
    this.loadWaiters();
  }

  loadWaiters() {
    this.userService.getAllTpv().subscribe({
      next: (users: any[]) => {
        this.waiters = users.filter((u: any) => u.role === 'staff' || u.role === 'waiter');
      },
      error: (err: any) => console.error('Error loading waiters:', err),
    });
  }

  selectWaiter(waiter: any) {
    this.onSelected.emit(waiter);
  }

  cancel() {
    this.onCancel.emit();
  }
}

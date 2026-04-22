import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular/standalone';
import { SidebarComponent } from '../../../components/sidebar/sidebar.component';
import { CashShiftService } from '../../../services/api/cash-shift.service';
import { CashShiftSummary, ClosedCashShiftSummary } from '../../../types/cash-shift.model';

@Component({
  selector: 'app-cash-shifts',
  standalone: true,
  imports: [IonContent, CommonModule, FormsModule, SidebarComponent],
  templateUrl: './cash-shifts.page.html',
  styleUrls: ['./cash-shifts.page.scss'],
})
export class CashShiftsPage implements OnInit {
  private cashShiftService = inject(CashShiftService);

  currentShift: CashShiftSummary | null = null;
  lastClosedShift: ClosedCashShiftSummary | null = null;
  loading = false;
  submitting = false;
  feedback: { type: 'success' | 'error'; message: string } | null = null;

  openingCash = 0;
  openingNotes = '';
  countedCash = 0;
  closingNotes = '';

  ngOnInit(): void {
    this.loadCurrentShift();
  }

  loadCurrentShift() {
    this.loading = true;
    this.feedback = null;
    this.cashShiftService.getCurrent().subscribe({
      next: (shift) => {
        this.currentShift = shift;
        this.countedCash = shift?.expected_cash ?? 0;
        this.loading = false;
      },
      error: () => {
        this.currentShift = null;
        this.feedback = { type: 'error', message: 'No se pudo cargar el estado de caja.' };
        this.loading = false;
      },
    });
  }

  openShift() {
    this.submitting = true;
    this.feedback = null;

    this.cashShiftService.open(this.openingCash, this.openingNotes || undefined).subscribe({
      next: (shift) => {
        this.currentShift = shift;
        this.countedCash = shift.expected_cash;
        this.openingCash = 0;
        this.openingNotes = '';
        this.feedback = { type: 'success', message: 'Caja abierta correctamente.' };
        this.submitting = false;
      },
      error: (error) => {
        this.feedback = { type: 'error', message: error?.error?.message ?? 'No se pudo abrir la caja.' };
        this.submitting = false;
      },
    });
  }

  closeShift() {
    if (!this.currentShift || this.submitting) {
      return;
    }

    this.submitting = true;
    this.feedback = null;

    this.cashShiftService.close(this.currentShift.uuid, this.countedCash, this.closingNotes || undefined).subscribe({
      next: (shift) => {
        this.lastClosedShift = shift;
        this.currentShift = null;
        this.countedCash = 0;
        this.closingNotes = '';
        this.feedback = { type: 'success', message: 'Caja cerrada correctamente.' };
        this.submitting = false;
      },
      error: (error) => {
        this.feedback = { type: 'error', message: error?.error?.message ?? 'No se pudo cerrar la caja.' };
        this.submitting = false;
      },
    });
  }

  formatCurrency(cents: number): string {
    return (cents / 100).toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
  }

  formatDate(value: string): string {
    return new Date(value).toLocaleString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }
}
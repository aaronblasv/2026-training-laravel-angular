import { Component, EventEmitter, Input, Output, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { UserService } from '../../services/api/user.service';
import { UploadService } from '../../services/api/upload.service';
import { User } from '../../types/user.model';

@Component({
  selector: 'app-profile-modal',
  standalone: true,
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [CommonModule, FormsModule],
  templateUrl: './profile-modal.component.html',
  styleUrls: ['./profile-modal.component.scss'],
})
export class ProfileModalComponent {
  @Input() visible = false;
  @Input() user: User | null = null;
  @Input() restaurantName = '';
  @Output() onClose = new EventEmitter<void>();

  editingPhoto = false;
  photoUrl = '';
  saving = false;

  private userService: UserService | null = null;

  constructor(private _userService: UserService, private uploadService: UploadService) {
    this.userService = _userService;
  }

  get roleLabel(): string {
    if (!this.user) return '';
    const roles: Record<string, string> = {
      admin: 'Administrador',
      supervisor: 'Supervisor',
      waiter: 'Camarero',
    };
    return roles[this.user.role] ?? this.user.role;
  }

  startEditPhoto() {
    this.editingPhoto = true;
    this.photoUrl = this.user?.image_src ?? '';
  }

  cancelEditPhoto() {
    this.editingPhoto = false;
    this.photoUrl = '';
  }

  onFileSelected(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    this.uploadService.uploadImage(file).subscribe({
      next: (url) => this.photoUrl = url,
      error: (err: any) => console.error('Upload error:', err),
    });
  }

  savePhoto() {
    if (!this.user || !this.userService) return;
    this.saving = true;
    this.userService.updatePhoto(this.user.uuid, this.photoUrl || null).subscribe({
      next: () => {
        if (this.user) {
          this.user.image_src = this.photoUrl || null;
        }
        this.editingPhoto = false;
        this.saving = false;
      },
      error: () => {
        this.saving = false;
      },
    });
  }

  removePhoto() {
    this.photoUrl = '';
    this.savePhoto();
  }
}

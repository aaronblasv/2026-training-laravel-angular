export interface User {
  uuid: string;
  name: string;
  email: string;
  role: string;
  active: boolean;
  image_src: string | null;
}

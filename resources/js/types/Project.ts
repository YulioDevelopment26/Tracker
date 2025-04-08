export interface Project {
    id: number;
    name: string;
    description: string;
    developer_user_id: number | null;
    admin_user_id: number;
    status: string;
}

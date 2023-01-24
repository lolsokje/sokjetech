import Series from '@/Interfaces/Series';
import Permission from '@/Interfaces/Permission';
import User from '@/Interfaces/User';

export default interface Universe {
    id: string,
    name: string,
    visibility: number,
    user?: User | null,
    series?: Series,
    can?: Permission,
}

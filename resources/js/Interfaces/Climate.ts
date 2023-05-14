import Condition from '@/Interfaces/Condition';

export default interface Climate {
    id: string,
    short_name: string,
    long_name: string,
    conditions: Condition[],
}

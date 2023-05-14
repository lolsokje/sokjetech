import Condition from '@/Interfaces/Condition';

export default interface Climate {
    name: string,
    conditions: Condition[],
}

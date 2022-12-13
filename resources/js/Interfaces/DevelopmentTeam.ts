import DevelopmentItem from '@/Interfaces/DevelopmentItem';

export default interface DevelopmentTeam extends DevelopmentItem {
    name: string,
    style: string,
    accent_colour: string,
}

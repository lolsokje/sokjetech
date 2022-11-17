interface Universe {
    id: string,
    name: string,
    visibility: number,
    user?: User | null,
    series?: Series,
    can?: Permission,
}

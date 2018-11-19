import React from 'react';
import { DataTable, TableHeader } from 'react-mdl';

const Users = ({ users }) => {

    return (
        <div style={{ width: '50%', margin: 'auto', marginTop: '5%' }} align="center">
            <DataTable
                shadow={0}
                rows={users}
            >
                <TableHeader numeric name="id">ID</TableHeader>
                <TableHeader name="username">Username</TableHeader>
                <TableHeader name="role">Role</TableHeader>
            </DataTable>
        </div>
    );
}

export default Users;
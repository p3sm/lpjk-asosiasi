import React, { Component } from 'react';
import { Button, Table } from 'react-bootstrap';

// import { Container } from './styles';

const formatter = new Intl.NumberFormat("id-ID", {
  style: "decimal",
  currency: "IDR"
});

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      isUpdate: false,
      nilai_proyek: 0,
      delete: false
    }

  }

  render() {
    return(
      <div>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Proyek</th>
              <th>Jabatan</th>
              <th>Nilai</th>
              <th>Tanggal</th>
              <th>Lokasi</th>
              {!this.props.viewOnly && (
                <th colSpan={2}>Action</th>
              )}
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Proyek}</td>
                <td>{d.Jabatan}</td>
                <td>{formatter.format(d.Nilai)}</td>
                <td>{d.Tgl_Mulai} - {d.Tgl_Selesai}</td>
                <td>{d.Lokasi}</td>
                {!this.props.viewOnly && (<td><Button variant="outline-warning" size="sm" onClick={() => this.props.onUpdateClick(d)}><span className="cui-pencil"></span> Ubah</Button></td>)}
                {!this.props.viewOnly && (<td><Button variant="outline-danger" size="sm" onClick={() => this.props.onDeleteClick(d)}><span className="cui-trash"></span> Delete</Button></td>)}
              </tr>
            ))}
          </tbody>
        </Table>
      </div>
    )
  }
}

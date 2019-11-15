import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table } from 'react-bootstrap';
import Datetime from 'react-datetime'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
    }

  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  onUploadChangeHandler = event => {
    $( event.target ).siblings("label").addClass("selected")
    $( event.target ).siblings("label").append(" (" + event.target.files[0].name + ")")

    switch(event.target.id){
      case "file_pengalaman":
        this.setState({ file_pengalaman: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama_bu", this.state.nama_bu);
    formData.append("nrbu", this.state.nrbu);
    formData.append("alamat", this.state.alamat);
    formData.append("jenis_bu", this.state.jenis_bu);
    formData.append("jabatan", this.state.jabatan);
    formData.append("tgl_mulai", this.state.tgl_mulai);
    formData.append("tgl_selesai", this.state.tgl_selesai);
    formData.append("role_pekerjaan", this.state.role_pekerjaan);
    formData.append("file_pengalaman", this.state.file_pengalaman);

    axios.post(`/api/organisasi/create`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false, showFormAdd: false})
      this.resetState()
      this.props.refreshData()

      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message);
    })
  }

  resetState = () => {
    this.setState({
      id_personal: "",
      nama_bu: "",
      nrbu: "",
      alamat: "",
      jenis_bu: "",
      jabatan: "",
      tgl_mulai: "",
      tgl_selesai: "",
      role_pekerjaan: "",
      file_pengalaman: ""
    })
  }

  render() {
    return(
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormAdd: true})}><span className="fa fa-edit"></span>Tambah Data</Button>
        <Table bordered>
          <tbody>
            <tr>
              <th>Nama Organisasi</th>
              <th>Jabatan</th>
              <th>Pekerjaan</th>
              <th>Tanggal</th>
              <th>Alamat</th>
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Badan_Usaha}</td>
                <td>{d.Jabatan}</td>
                <td>{d.Role_Pekerjaan}</td>
                <td>{d.Tgl_Mulai} - {d.Tgl_Selesai}</td>
                <td>{d.Alamat}</td>
              </tr>
            ))}
          </tbody>
        </Table>
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.showFormAdd}>
          <Modal.Header closeButton>
            <Modal.Title>Tambah Data</Modal.Title>
          </Modal.Header>
          <Modal.Body>
            <Form>
              <Row>
                <Col>
                  <Form.Group>
                    <Form.Label>Nama Instansi</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({nama_bu: e.target.value})} value={this.state.nama_bu}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Jabatan</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({jabatan: e.target.value})} value={this.state.jabatan}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Alamat</Form.Label>
                    <Form.Control as="textarea" row="3" onChange={(e) => this.setState({alamat: e.target.value})} value={this.state.alamat}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Jenis Instansi</Form.Label>
                    <Form.Control as="select" name="jenis_bu" onChange={(e) => this.setState({jenis_bu: e.target.value})}>
                      <option value="">-- Pilih Jenis Instansi --</option>
                      <option value="1">Formal Pemerintah</option>
                      <option value="2">Formal Swasta</option>
                      <option value="3">Non-Formal</option>
                    </Form.Control>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Tanggal Awal</Form.Label>
                    <Datetime value={this.state.tgl_mulai} onChange={(e) => this.setState({tgl_mulai: e.format("YYYY-MM-DD")})} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tanggal Akhir</Form.Label>
                    <Datetime value={this.state.tgl_selesai} onChange={(e) => this.setState({tgl_selesai: e.format("YYYY-MM-DD")})} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Deskripsi Pekerjaan</Form.Label>
                    <Form.Control as="textarea" row="3" onChange={(e) => this.setState({role_pekerjaan: e.target.value})} value={this.state.role_pekerjaan}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_pengalaman" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_pengalaman">Pengalaman Organisasi</label>
                  </div>
                </Col>
              </Row>
            </Form>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="light" onClick={this.handleClose}>
              Cancel
            </Button>
            <Button className="d-flex" disabled={this.state.submiting} variant="primary" onClick={!this.state.submiting ? this.handleSubmit : null}>
              {this.state.submiting ? 'Submiting...' : 'Submit'}
            </Button>
          </Modal.Footer>
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="none" />
        </Modal>
      </div>
    )
  }
}

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
      role_pekerjaan: "",
      id_personal: this.props.id_personal,
      isUpdate: false
    }

  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  openUpdateForm = (data) => {
    this.setState({
      showFormAdd: true,
      isUpdate: true,
      ID_Personal_Pengalaman: data.ID_Personal_Pengalaman,
      id_personal: data.ID_Personal,
      nama_bu: data.Nama_Badan_Usaha,
      nrbu: data.NRBU,
      alamat: data.Alamat,
      jenis_bu: data.Jenis_BU,
      jabatan: data.Jabatan,
      tgl_mulai: data.Tgl_Mulai,
      tgl_selesai: data.Tgl_Selesai,
      role_pekerjaan: data.Role_Pekerjaan,
    })
  }


  onUploadChangeHandler = event => {
    var size = event.target.files[0].size
    var label = $( event.target ).siblings("label")

    if(size > 20000000){
      Alert.error('Max file size 20mb')

      return
    }

    label.addClass("selected")
    label.html(event.target.files[0].name)

    switch(event.target.id){
      case "file_pengalaman":
        label.prepend("Upload Pengalaman Organisasi ")
        this.setState({ file_pengalaman: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("ID_Personal_Pengalaman", this.state.ID_Personal_Pengalaman);
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama_bu", this.state.nama_bu);
    formData.append("nrbu", this.state.nrbu);
    formData.append("alamat", this.state.alamat);
    formData.append("jenis_bu", this.state.jenis_bu);
    formData.append("jabatan", this.state.jabatan);
    formData.append("tgl_mulai", this.state.tgl_mulai);
    formData.append("tgl_selesai", this.state.tgl_selesai);
    formData.append("role_pekerjaan", this.state.role_pekerjaan != "" ? this.state.role_pekerjaan : "-");
    formData.append("file_pengalaman", this.state.file_pengalaman);

    var uri = this.state.isUpdate ? "/api/organisasi/update" : "/api/organisasi/create"

    axios.post(uri, formData, {
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
              <th>Action</th>
            </tr>
            {this.props.data.map((d) => (
              <tr>
                <td>{d.Nama_Badan_Usaha}</td>
                <td>{d.Jabatan}</td>
                <td>{d.Role_Pekerjaan}</td>
                <td>{d.Tgl_Mulai} - {d.Tgl_Selesai}</td>
                <td>{d.Alamat}</td>
                <td><Button variant="outline-warning" size="sm" onClick={() => this.openUpdateForm(d)}><span className="cui-pencil"></span> Ubah</Button></td>
              </tr>
            ))}
          </tbody>
        </Table>
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.showFormAdd}>
          <Modal.Header closeButton>
            <Modal.Title>{this.state.isUpdate ? "Ubah" : "Tambah"} Data</Modal.Title>
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
                      <option value="1" selected={this.state.jenis_bu == "1" ? "selected" : ""}>Formal Pemerintah</option>
                      <option value="2" selected={this.state.jenis_bu == "2" ? "selected" : ""}>Formal Swasta</option>
                      <option value="3" selected={this.state.jenis_bu == "3" ? "selected" : ""}>Non-Formal</option>
                    </Form.Control>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Tanggal Awal</Form.Label>
                    <Datetime closeOnSelect={true} inputProps={{ placeholder: 'contoh: 1980-01-01'}} value={this.state.tgl_mulai} dateFormat="YYYY-MM-DD" onChange={(e) => {
                      try {
                        this.setState({tgl_mulai: e.format("YYYY-MM-DD")})
                      } catch (err) {
                        this.setState({tgl_mulai: e})
                      }
                    }} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tanggal Akhir</Form.Label>
                    <Datetime closeOnSelect={true} inputProps={{ placeholder: 'contoh: 1980-01-01'}} value={this.state.tgl_selesai} dateFormat="YYYY-MM-DD" onChange={(e) => {
                      try {
                        this.setState({tgl_selesai: e.format("YYYY-MM-DD")})
                      } catch (err) {
                        this.setState({tgl_selesai: e})
                      }
                    }} timeFormat={false} />
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Deskripsi Pekerjaan</Form.Label>
                    <Form.Control as="textarea" row="3" onChange={(e) => this.setState({role_pekerjaan: e.target.value})} value={this.state.role_pekerjaan}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_pengalaman" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_pengalaman">Upload Pengalaman Organisasi</label>
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

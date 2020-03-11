import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import ViewPendidikan from './ViewPendidikan'
import MSelectCountry from './MSelectCountry'
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import MSelectPendidikan from './MSelectPendidikan'
import axios from 'axios'
import Alert from 'react-s-alert';
import SweetAlert from 'react-bootstrap-sweetalert';

// import { Container } from './styles';

export default class components extends Component {
  constructor(props){
    super(props)

    this.state = {
      showFormAdd: false,
      submiting: false,
      id_personal: this.props.id_personal,
      isUpdate: false,
      file_data_pendidikan: "",
      file_keterangan_sekolah: "",
      delete: false,
      jenjang: this.props.tipe_profesi == 1 ? 4 : 2,
      negara: "ID",
      jurusan: this.props.tipe_profesi == 1 ? "Teknik Sipil" : "Bangunan",
    }
  }

  componentDidMount(){
  }

  handleClose = () => {
    this.setState({showFormAdd: false})
  }

  onProvinsiChange = (data) => {
    this.setState({provinsi: data.value})
    this.selectKabupaten.getKabupaten(data.value)
  }

  onNegaraChange = (data) => {
    console.log(data)
    this.setState({negara: data.value, provinsi: data.value == "ID" ? "" : 99, kabupaten: data.value == "ID" ? "" : 9999})

    if(data.value != "ID"){
      this.selectKabupaten.getKabupaten(99)
    }
  }

  openUpdateForm = (data) => {
    this.setState({
      showFormAdd: true,
      isUpdate: true,
      ID_Personal_Pendidikan: data.ID_Personal_Pendidikan,
      id_personal: data.ID_Personal,
      nama: data.Nama_Sekolah,
      alamat: data.Alamat1,
      provinsi: data.ID_Propinsi,
      kabupaten: data.ID_Kabupaten,
      negara: data.ID_Countries,
      tahun: data.Tahun,
      jenjang: data.Jenjang,
      jurusan: data.Jurusan,
      no_ijazah: data.No_Ijazah,
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
    label.css("border", "#6ab04c solid 1px")
    label.css("background", "#f0f3f1")
    
    var check = '<i class="fa fa-check" aria-hidden="true" style="color: #6cae64;margin-right: 10px;"></i>';

    switch(event.target.id){
      case "file_ijazah":
        label.prepend(check + "Upload Ijazah ")
        this.setState({ file_ijazah: event.target.files[0] })
        break;
      case "file_data_pendidikan":
        label.prepend(check + "Upload Data Pendidikan ")
        this.setState({ file_data_pendidikan: event.target.files[0] })
        break;
      case "file_keterangan_sekolah":
        label.prepend(check + "Upload Surat Keterangan dari Sekolah ")
        this.setState({ file_keterangan_sekolah: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("ID_Personal_Pendidikan", this.state.ID_Personal_Pendidikan);
    formData.append("id_personal", this.state.id_personal);
    formData.append("nama", this.state.nama);
    formData.append("alamat", this.state.alamat);
    formData.append("provinsi", this.state.provinsi);
    formData.append("kabupaten", this.state.kabupaten);
    formData.append("negara", this.state.negara);
    formData.append("tahun", this.state.tahun);
    formData.append("jenjang", this.state.jenjang);
    formData.append("jurusan", this.state.jurusan);
    formData.append("no_ijazah", this.state.no_ijazah);
    formData.append("file_ijazah", this.state.file_ijazah);

    formData.append("file_data_pendidikan", this.state.negara == "ID" && this.state.file_data_pendidikan == "" ? this.state.file_ijazah : this.state.file_data_pendidikan);
    formData.append("file_keterangan_sekolah", this.state.negara == "ID" && this.state.file_keterangan_sekolah == "" ? this.state.file_ijazah :  this.state.file_keterangan_sekolah);

    var uri = this.state.isUpdate ? "/api/pendidikan/update" : "/api/pendidikan"

    axios.post(uri, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false, showFormAdd: false})
      this.resetState()
      this.props.refreshData()

      Alert.success(response.data.message)
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message)
    })
  }

  confirmDelete = (id) => {
    console.log(id)
    this.setState({delete: true, deleteId: id})
  }

  deletePendidikan = (id) => {
    this.setState({deleting: true})

    var formData = new FormData();
    formData.append("id_personal_pendidikan", id);
      
    axios.post("/api/pendidikan/delete", formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({deleting: false, delete: false})
      this.props.refreshData()
      
      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({deleting: false, delete: false})
      Alert.error(err.response.data.message);
    })
  }

  resetState = () => {
    this.setState({
      nama: "",
      alamat: "",
      provinsi: "",
      kabupaten: "",
      negara: "",
      tahun: "",
      jenjang: "",
      jurusan: "",
      no_ijazah: "",
      file_ijazah: "",
      file_data_pendidikan: "",
      file_keterangan_sekolah: "",
    })
  }

  render() {
    return(
      <div>
        <Button variant="outline-info" className="mb-3" onClick={() => this.setState({showFormAdd: true})}><span className="fa fa-edit"></span>Tambah Data</Button>
        <ViewPendidikan data={this.props.data} onUpdateClick={(d) => this.openUpdateForm(d)} onDeleteClick={(d) => this.confirmDelete(d.ID_Personal_Pendidikan)} />
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
                    <Form.Label>Nama Sekolah / Perguruan Tinggi</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({nama: e.target.value})} value={this.state.nama}></Form.Control>
                  </Form.Group>

                  <MSelectPendidikan value={this.state.jenjang} onChange={(data) => this.setState({jenjang: data.value})} />

                  <MSelectCountry value={this.state.negara} onChange={(data) => this.onNegaraChange(data)} />

                  <MSelectProvinsi value={this.state.provinsi} onChange={(data) => this.onProvinsiChange(data)} disabled={this.state.negara != "ID"} />

                  <MSelectKabupaten value={this.state.kabupaten} provinsiId={this.state.provinsi} onRef={ref => (this.selectKabupaten = ref)} onChange={(data) => this.setState({kabupaten: data.value})} disabled={this.state.negara != "ID"}/>

                  <Form.Group>
                    <Form.Label>Alamat</Form.Label>
                    <Form.Control as="textarea" row="3" onChange={(e) => this.setState({alamat: e.target.value})} value={this.state.alamat}></Form.Control>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>No. Ijazah</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({no_ijazah: e.target.value})} value={this.state.no_ijazah}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Program Studi</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({jurusan: e.target.value})} onFocus={(event) => event.target.select()} value={this.state.jurusan}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Tahun Lulus</Form.Label>
                    <Form.Control placeholder="" onChange={(e) => this.setState({tahun: e.target.value})} value={this.state.tahun}></Form.Control>
                  </Form.Group>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_ijazah" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_ijazah">Upload Ijazah</label>
                  </div>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_data_pendidikan" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_data_pendidikan">Upload Data Pendidikan</label>
                  </div>
                  <div class="custom-file mb-3">
                    <input type="file" class="custom-file-input" id="file_keterangan_sekolah" onChange={this.onUploadChangeHandler}></input>
                    <label class="custom-file-label" for="file_keterangan_sekolah">Upload Surat Keterangan dari Sekolah</label>
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
          <Alert stack={{limit: 3}} position="top-right" offset="40" effect="slide" timeout="2000" />
        </Modal>
          
          <SweetAlert
            show={this.state.delete}
            warning
            showCancel
            title="Hapus Data"
            btnSize="md"
            confirmBtnBsStyle='success'
            cancelBtnText="Close"
            confirmBtnText={this.state.deleting ? "Deleting..." : "Delete"}
            onConfirm={() => this.deletePendidikan(this.state.deleteId)}
            onCancel={() => this.setState({delete: false})}
          >Anda yakin akan menghapus data ini?</SweetAlert>
      </div>
    )
  }
}

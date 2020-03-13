import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Tabs, Tab, Form, Spinner, Col, Row, Modal} from 'react-bootstrap';
import axios from 'axios'
import moment from 'moment'
import SweetAlert from 'react-bootstrap-sweetalert';
import Alert from 'react-s-alert';

import ViewBiodata from './ViewBiodata'
import ViewPendidikan from './ViewPendidikan'
import ViewKursus from './ViewKursus'
import ViewOrganisasi from './ViewOrganisasi'
import ViewProyek from './ViewProyek'
import ViewKualifikasi from './ViewKualifikasi'
import InputNewBiodata from './InputNewBiodata'

export default class Personal extends Component {
    constructor(props){
      super(props);

      this.state = {
        data: [],
        loading: false,
        id_personal: "",
        biodata: null,
        pendidikan: [],
        kursus: [],
        organisasi: [],
        proyek: [],
        kualifikasi_tt: [],
        error: false,
        showFormAddBiodata: false
      }
    }

    componentDidMount(){
    }

    onNikSearch(e){
      e.preventDefault()

      if(!this.state.loading){
        if(this.state.id_personal == ""){
          Alert.error('ID Personal / KTP tidak boleh kosong');

          return
        } else if(this.state.id_personal.length != 16){
          Alert.error('ID Personal / KTP harus 16 karakter')

          return
        }

        this.getBiodata()
      }
    }

    getBiodata(){
      this.setState({loading: true, biodata: null})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/biodata`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          biodata: result.data,
          loading: false
        })

        this.getPendidikan()
        this.getKursus()
        this.getOrganisasi()
        this.getProyek()
        this.getKualifikasi()
        
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getPendidikan(){
      this.setState({pendidikan: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.get(`/api/pendidikan/` + this.state.id_personal).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          pendidikan: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getKursus(){
      this.setState({kursus: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/kursus`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          kursus: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getOrganisasi(){
      this.setState({organisasi: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/organisasi`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          organisasi: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getProyek(){
      this.setState({proyek: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/proyek`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          proyek: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    getKualifikasi(){
      this.setState({kualifikasi_tt: []})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/kualifikasi_tt_99`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          kualifikasi_tt: result.data,
          loading: false
        })
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          // error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    handleChange(event){
      this.setState({
        id_personal: event.target.value
      })
    }

    render() {
        return (
          <div>
            <Form onSubmit={(e) => this.onNikSearch(e)}>
              <div className="form-group">
                <label htmlFor="nik">ID Personal / KTP</label>
                <div className="input-group">
                  <Form.Control id="nama" placeholder="No. KTP" onChange={(event) => this.handleChange(event)} value={this.state.id_personal}></Form.Control>
                  <div className="input-group-append">
                    <button className={"btn btn-" + (this.state.loading ? "light" : "primary")} onClick={(e) => this.onNikSearch(e)} type="button">{this.state.loading ? "Mencari..." : "Cari Data"}</button>
                  </div>
                </div>
              </div>
              <Row style={{justifyContent: "center", display: this.state.loading ? "flex" : "none"}}>
                <Spinner style={{alignSelf: "center"}} animation="border" variant="primary" />
              </Row>
              
              <InputNewBiodata tipe_profesi={1} id_personal={this.state.id_personal} visible={this.state.showFormAddBiodata} onClose={() => this.setState({showFormAddBiodata: false})} onSuccess={() => this.setState({showFormAddBiodata: false}, () => this.getBiodata())} />
              
              <SweetAlert
                show={this.state.error}
                danger
                title="Maaf"
                btnSize="md"
                confirmBtnBsStyle='default'
                confirmBtnText="Close"
                onConfirm={() => this.setState({error: false})}
              >{this.state.errorMsg}</SweetAlert>
              {this.state.biodata != null && (
                <Tabs defaultActiveKey="biodata" id="">
                  <Tab eventKey="biodata" title="Biodata">
                    <ViewBiodata tipe_profesi={1} data={this.state.biodata} refreshData={() => this.getBiodata()}/>
                  </Tab>
                  <Tab eventKey="pendidikan" title="Pendidikan">
                    <ViewPendidikan data={this.state.pendidikan} viewOnly={true} />
                  </Tab>
                  <Tab eventKey="kursus" title="Kursus">
                    <ViewKursus data={this.state.kursus} viewOnly={true}/>
                  </Tab>
                  <Tab eventKey="organisasi" title="Pengalaman Organisasi">
                    <ViewOrganisasi data={this.state.organisasi} viewOnly={true}/>
                  </Tab>
                  <Tab eventKey="proyek" title="Pengalaman Proyek">
                    <ViewProyek data={this.state.proyek} viewOnly={true}/>
                  </Tab>
                  <Tab eventKey="kualifikasi" title="Klasifikasi Kualifikasi">
                    <ViewKualifikasi tipe_profesi={2} id_personal={this.state.id_personal} data={this.state.kualifikasi_tt} refreshData={() => this.getKualifikasi()}/>
                  </Tab>
                </Tabs>
              )}
            </Form>
            <Alert stack={{limit: 3}} position='top-right' offset={50} effect='slide' timeout={2000}/>
          </div>
        );
    }
}

if (document.getElementById('pengajuan_skt')) {
    ReactDOM.render(<Personal />, document.getElementById('pengajuan_skt'));
}
